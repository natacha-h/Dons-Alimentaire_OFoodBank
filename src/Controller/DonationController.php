<?php

namespace App\Controller;

use App\Entity\Address;
use App\Utils\Rewarder;
use App\Entity\Donation;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Utils\Addresser;
use App\Form\DonationType;
use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use App\Repository\CategoryRepository;
use App\Repository\DonationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
/**
 * @Route("/dons", name="donation_")
 */
class DonationController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(DonationRepository $donationRepository, PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepository)
    {
        $donations = $donationRepository->findDonationWithProducts();
        $donationsList = $paginator->paginate(
            $donationRepository->findByStatusQuery(),
            $request->query->getInt('page', 1),
            10
        );
        $expiryDateArray = [];
        foreach($donations as $donation){
            $currentExpiry = false;

            foreach($donation->getProducts() as $product){
                $expiryDate = $product->getExpiryDate();
        
                // Premier tour de boucle on récupere le current
                if($currentExpiry == false){
                    $currentExpiry = $expiryDate;
                }
                // Nombre de secondes écoulées plus faible <=> date plus proche
                if($currentExpiry >= $expiryDate){
                    $currentExpiry = $expiryDate;
                }
            }
            $expiryDateArray[$donation->getId()] = $currentExpiry;
        }
        // on veut afficher un formulaire de tri par catégorie
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 1.récupérer la catégorie sélectionnée
            $category = $form->getData();
            $categoryName = $category->getName();
            // récupérer en BD la catégorie concernée
            $category = $categoryRepository->findByName($categoryName);
            // récupérer l'id de la catégorie
            $catId = $category[0]->getId();
            // 2. ne récupérer que les dons correspondant à cette catégorie et ajouter la pagination
            $filteredDonation = $paginator->paginate(
                $donationRepository->findFilteredDonationWithProducts($catId),
                $request->query->getInt('page', 1),
                10
            );
            // 3. renvoyer la vue adéquate
            return $this->render('donation/filtered-list.html.twig', [
                'id' => $catId,
                'category' => $categoryName,
                'donations' => $filteredDonation,
                'expiryDateArray' => $expiryDateArray,
                'form'=>$form->createView(),
            ]);
        }
            return $this->render('donation/list.html.twig', [
                'donations' => $donationsList,
                'expiryDateArray' => $expiryDateArray,
                'form'=>$form->createView(),
            ]);
        
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show($id, DonationRepository $donationRepository)
    {
        // on récupère le don
        $donation = $donationRepository->findDonationWithAllDetails($id);
        // on récupère la collection de user afin d'identifier le donateur
        $users = $donation->getUsers();
        // pour chaque utilisateur
        $collector = null;
        foreach ($users as $user){
            // on récupère le tableau de rôle et on boucle dessus            
            // si le rôle est 'ROLE_ASSOC', on identifie l'utilisateur
            if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                $collector = $user;
            } 
            // si le rôle est 'ROLE_GIVER', on identifie l'utilisateur comme étant le donateur
            if ('ROLE_GIVER' == $user->getRole()->getCode()){
                $giver = $user;
            }
            
        }
        return $this->render('donation/show.html.twig', [
            'donation' => $donation,
            'giver' => $giver,
            'collector' => $collector,
        ]);
    }

    /**
     * @Route("/{id}/select", name="select", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function select(Donation $donation, StatusRepository $statusRepository, EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        //on vérifie le status actuel du don
        $currentStatus = $donation->getStatus()->getName();        
        // si le don est déjà réservé
        if ($donation->getStatus()->getReserved() == $currentStatus){
            // on affiche un flashMessage pour informer l'utilisateur
            $this->addFlash(
                'danger',
                'Le don a été réservé pendant que vous regardiez les détails'
            );
        } //sinon c'est bon
        else {
            // on crée un nouvel objet Status 
            $newStatus = $statusRepository->findOneByName('Réservé');
            // on change le status de la donnation
            $donation->setStatus($newStatus);
            // on ajoute l'id du demandeur à la donnation
            $donation->addUser($this->getUser());
            // on persist et on flush
            $em->persist($donation);
            $em->flush();
            // ajout d'un flash message
            $this->addFlash(
                'success',
                'La demande de réservation est bien prise en compte'
            );
            // Envoi de mails lors de la réservation
            $donationUsers = $donation->getUsers();
            $donationTitle = $donation->getTitle();
            $donationId = $donation->getId();
            foreach ($donationUsers as $user){
                // Si on est une Association
                    if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                    $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                    $firstName = $user->getFirstName();
                    $lastName = $user->getLastName();         
                    $mail = (new \Swift_Message('Confirmation de réservation du don : '.$donationTitle))
                    ->setFrom('ofoodbank@gmail.com')
                    ->setTo($email)
                    ->setBody(
                            $this->renderView(
                                'mailer/mail-reservation-assoc.html.twig',
                                [
                                    'email' => $email,
                                    'donationTitle' => $donationTitle,
                                    'donationId' => $donationId,
                                    'firstName' => $firstName,
                                    'lastName' => $lastName
                                ]
                            ),
                            'text/html'
                        );
                                
                $mailer->send($mail);
                }
                // Si on est Donateur 
                    if ('ROLE_GIVER' == $user->getRole()->getCode()){
                    $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                    $userId = $user->getId();
                    $firstName = $user->getFirstName();
                    $lastName = $user->getLastName(); 
                    $mail = (new \Swift_Message("Votre don : $donationTitle a été réservé"))
                    ->setFrom('ofoodbank@gmail.com')
                    ->setTo($email)
                    ->setBody(
                            $this->renderView(
                                'mailer/mail-reservation-giver.html.twig',
                                [
                                    'email' => $email,
                                    'donationTitle' => $donationTitle,
                                    'donationId' => $donationId,
                                    'firstName' => $firstName,
                                    'lastName' => $lastName,
                                    'userId' => $userId
                                ]
                            ),
                            'text/html'
                        );
                $mailer->send($mail);
                }
            }
        }
        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
        ]);
    }

    /**
     * @Route("/{id}/deselect", name="deselect", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function deselect(Donation $donation, EntityManagerInterface $em, StatusRepository $statusRepository, \Swift_Mailer $mailer)
    {
        // Envoi de mails lors de la réservation
        $donationUsers = $donation->getUsers();
        $donationTitle = $donation->getTitle();
        $donationId = $donation->getId();

        foreach ($donationUsers as $user){
            // Si on est Donateur 
                if ('ROLE_GIVER' == $user->getRole()->getCode()){
                $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                $userId = $user->getId();
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName(); 
                $mail = (new \Swift_Message($donationTitle.' : Réservation annulée'))
                ->setFrom('ofoodbank@gmail.com')
                ->setTo($email)
                ->setBody(
                        $this->renderView(
                            'mailer/mail-canceled-reservation-giver.html.twig',
                            [
                                'email' => $email,
                                'donationTitle' => $donationTitle,
                                'donationId' => $donationId,
                                'firstName' => $firstName,
                                'lastName' => $lastName,
                                'userId' => $userId
                            ]
                        ),
                        'text/html'
                    );
            $mailer->send($mail);
            }
                // Si on est une association 
                if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                    $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                    $userId = $user->getId();
                    $firstName = $user->getFirstName();
                    $lastName = $user->getLastName(); 
                    $mail = (new \Swift_Message($donationTitle.' : Réservation annulée'))
                    ->setFrom('ofoodbank@gmail.com')
                    ->setTo($email)
                    ->setBody(
                            $this->renderView(
                                'mailer/mail-canceled-reservation-assoc.html.twig',
                                [
                                    'email' => $email,
                                    'donationTitle' => $donationTitle,
                                    'donationId' => $donationId,
                                    'firstName' => $firstName,
                                    'lastName' => $lastName,
                                    'userId' => $userId
                                ]
                            ),
                            'text/html'
                        );
                $mailer->send($mail);
                }
            }
        // on crée un nouvel objet Status
        $newStatus = $statusRepository->findOneByName('Dispo');
        // on attribue le status au don
        $donation->setStatus($newStatus);
        // on retire l'id du user 
        $donation->removeUser($this->getUser());
        // on persist et on flush
        $em->persist($donation);
        $em->flush();
        // ajout d'un Flash Message
        $this->addFlash(
            'success',
            'Vous avez bien annulé la réservation de ce don'
        );
        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
        ]);
    }
    
    /**
     * @Route("/api/address/{id}/coordonates", name="coordonates", methods={"POST"})
     */
    public function getCoordonates(Donation $donation, Addresser $addresser){
        // On récupere l'adresse du don concerné
        $address = $donation->getAddress();
        // On récupere les infos de son adresse pour construire l'url
        $number = $address->getNumber();
        // On utilise le zipcode pour récupérer les coordonées GPS
        $zipCode = $address->getZipCode();
        // On remplace les espaces du nom de la rue par des + grâce au service
        $street1 = $address->getStreet1();
        $plussedStreet = $addresser->addresser($street1);
        // On récupere le contenu de la page (retour json sur la page donc on recupere du json) avec ou sans numéro
        if($number != null){
            // On construit l'url avec les valeurs de la donation concernée avec chiffre
            $response = file_get_contents('https://nominatim.openstreetmap.org/search?q='.$number.'+'. $plussedStreet .',+'. $zipCode .'&format=json&polygon=1&addressdetails=1&limit=1&countrycodes=fr&email=rpelletier86@gmail.com');
        } else{
            // On construit l'url avec les valeurs de la donation concernée sans chiffre
            $response = file_get_contents('https://nominatim.openstreetmap.org/search?q=' . $plussedStreet . ',+' . $zipCode . '&format=json&polygon=1&addressdetails=1&limit=1&countrycodes=fr&email=rpelletier86@gmail.com');
        }
        // On décode la réponse sous forme de tableau
        $response = json_decode($response, true);
        // Si le tableau est vide on retourne un code 0
        if(empty($response)){
            return $this->json([
                    'code' => 0
            ]);
        }
        // Sinon on retourne un code 1 et la réponse
        else {
            return $this->json([
                'coordonates' => $response,
                'code' => 1
                ]);
        }
    }

    /**
     * @Route("/{id}/accept", name="accept", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function acceptDonation(StatusRepository $statusRepository, Donation $donation, EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        // on crée un nouvel objet Status 
        $newStatus = $statusRepository->findOneByName('Donné');
        // on change le status de la donnation
        $donation->setStatus($newStatus);
        // on persist et on flush
        $em->persist($donation);
        $em->flush();
        $response = 'Don accepté';

        // Envoi de mails lors de la réservation
        $donationUsers = $donation->getUsers();
        $donationTitle = $donation->getTitle();
        $donationId = $donation->getId();

        foreach ($donationUsers as $user){
            // Si on est une Association
            if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();         
                $mail = (new \Swift_Message('Votre réservation pour le don '.$donationTitle.' a bien été acceptée'))
                ->setFrom('ofoodbank@gmail.com')
                ->setTo($email)
                ->setBody(
                        $this->renderView(
                            'mailer/mail-accepted-reservation-assoc.html.twig',
                            [
                                'email' => $email,
                                'donationTitle' => $donationTitle,
                                'donationId' => $donationId,
                                'firstName' => $firstName,
                                'lastName' => $lastName
                            ]
                        ),
                        'text/html'
                    );
                            
            $mailer->send($mail);
            }
            // Si on est Donateur 
                if ('ROLE_GIVER' == $user->getRole()->getCode()){
                $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                $userId = $user->getId();
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName(); 
                $mail = (new \Swift_Message($donationTitle.' : Réservation acceptée'))
                ->setFrom('ofoodbank@gmail.com')
                ->setTo($email)
                ->setBody(
                        $this->renderView(
                            'mailer/mail-accepted-reservation-giver.html.twig',
                            [
                                'email' => $email,
                                'donationTitle' => $donationTitle,
                                'donationId' => $donationId,
                                'firstName' => $firstName,
                                'lastName' => $lastName,
                                'userId' => $userId
                            ]
                        ),
                        'text/html'
                    );
            $mailer->send($mail);
            }
        }
        return $this->json([
            'response' => $response,
            'code' => 1
            ]);        
    }

    /**
     * @Route("/{id}/refuse", name="refuse", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function refuseDonation(Donation $donation, EntityManagerInterface $em, StatusRepository $statusRepository, \Swift_Mailer $mailer)
    {
            // Envoi de mails lors de la réservation
            $donationUsers = $donation->getUsers();
            $donationTitle = $donation->getTitle();
            $donationId = $donation->getId();
    
            foreach ($donationUsers as $user){
                // Si on est une Association
                if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                    $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                    $firstName = $user->getFirstName();
                    $lastName = $user->getLastName();         
                    $mail = (new \Swift_Message('Votre réservation pour le don '.$donationTitle.' a été refusée'))
                    ->setFrom('ofoodbank@gmail.com')
                    ->setTo($email)
                    ->setBody(
                            $this->renderView(
                                'mailer/mail-refused-reservation-assoc.html.twig',
                                [
                                    'email' => $email,
                                    'donationTitle' => $donationTitle,
                                    'donationId' => $donationId,
                                    'firstName' => $firstName,
                                    'lastName' => $lastName
                                ]
                            ),
                            'text/html'
                        );
                                
                $mailer->send($mail);
                }
                // Si on est Donateur 
                    if ('ROLE_GIVER' == $user->getRole()->getCode()){
                    $email = $user->getEmail(); // Déclaration de l'adresse de destination.
                    $userId = $user->getId();
                    $firstName = $user->getFirstName();
                    $lastName = $user->getLastName(); 
                    $mail = (new \Swift_Message($donationTitle.' : Réservation refusée'))
                    ->setFrom('ofoodbank@gmail.com')
                    ->setTo($email)
                    ->setBody(
                            $this->renderView(
                                'mailer/mail-refused-reservation-giver.html.twig',
                                [
                                    'email' => $email,
                                    'donationTitle' => $donationTitle,
                                    'donationId' => $donationId,
                                    'firstName' => $firstName,
                                    'lastName' => $lastName,
                                    'userId' => $userId
                                ]
                            ),
                            'text/html'
                        );
                $mailer->send($mail);
                }
            }
        // on crée un nouvel objet Status
        $newStatus = $statusRepository->findOneByName('Dispo');
        // on attribue le status au don
        $donation->setStatus($newStatus);
        // il faut supprimer l'association de la liste des users liée au don courant
            //1- on récupère la liste des utilisateurs liés au don
        $users = $donation->getUsers();
            //2- on boucle sur la collection pour récupérer le user_role 'ROLE_ASSOC'
        $asso = null;
        foreach ($users as $user){
            //si le role de user est 'ROLE_ASSOC', on le donne en valeur de la variable $asso
            if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                $asso = $user;
            }
        }
        // on retire l'id de l'association
        $donation->removeUser($asso);
        // on persist et on flush
        $em->persist($donation);
        $em->flush();
        $response = 'Don refusé';
        return $this->json([
            'response' => $response,
            'code' => 1
            ]);
       
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     */
    public function new(Request $request, CategoryRepository $cateRepo, EntityManagerInterface $em, StatusRepository $StatusRepo, Rewarder $rewarder, UserRepository $userRepo, \Swift_Mailer $mailer)
    {
        $donation = new Donation();
        $donation->setCreatedAt(new \Datetime());
        $donation->setUpdatedAt(new \Datetime());
        $form = $this->createForm(DonationType::class, $donation);
        $addressFormNumber = $request->request->get('number');
        $addressFormStreet1 = $request->request->get('street1');
        $addressFormStreet2 = $request->request->get('street2');
        $addressFormZipCode = $request->request->get('zipCode');
        $addressFormCity = $request->request->get('city');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //avant l'enregistrement d'un don je dois recupérer l'objet fichier qui n'est pas une chaine de caractère
            $file = $donation->getPicture();
            if(!is_null($file)){
                $extension = $file->guessExtension();
                
                if($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png' && $extension != 'gif' ){
                    $this->addFlash('danger', 'Le format de votre image ne correspond pas');
                    return $this->render('donation/new.html.twig', [
                        'form' => $form->createView()
                    ]);
                }
                //je génère un nom de fichier unique pour éviter d'écraser un fichier du meme nom & je concatène avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName().'.'.$extension;
                try {
                    //je déplace mon fichier dans le dossier souhaité
                    $file->move(
                        $this->getParameter('picture_directory'),
                        $fileName
                    );
                    
                } catch (FileException $e) {
                    dump($e);
                }
                $donation->setPicture($fileName);
            }
            // Je gère le fait de donner une image standard au don
            else {
                $fileName = 'default-image.jpg';
                $donation->setPicture($fileName);
            }
            // Je lui fournis un status disponible directement
            $status = $StatusRepo->findOneByName('Dispo');
            $donation->setStatus($status);
            
            // si les champs du formulaire sont vides, alors l'utilisateur a gardé l'adresse d'origine (= la sienne)
            if (null == $addressFormNumber && null == $addressFormStreet1 && null == $addressFormStreet2 && null == $addressFormZipCode && null == $addressFormCity) {
                // on attribue à la donation l'adresse du User/donateur
                ($donation->setAddress($this->getUser()->getAddress()));
            } else {// sinon c'est qu'il a choisi une autre adresse
                // on l'enregistre
                $donationAddress = new Address();
                
                // Je crée un tableau qui va contenir les erreurs vides.
                $errorList = [];
                // Si le numero est saisi alors je peux le setter
                if($addressFormNumber){
                    $donationAddress->setNumber($addressFormNumber);
                }
                // Si l'adresse n'est pas vide je la sette
                if(trim($addressFormStreet1) != ''){
                    $donationAddress->setStreet1($addressFormStreet1);
                }
                // Sinon je remplis le tableau d'erreur
                else{
                    $this->addFlash('warning', 'Veuillez renseigner un nom de rue');
                    $errorList['street'] = 'Veuillez renseigner le nom de la rue';
                }
                // Si complément est rempli alors je le sette
                if($addressFormStreet2){
                    $donationAddress->setStreet2($addressFormStreet2);
                }
                // Si zipCode pas vide alors je sette
                if(trim($addressFormZipCode) != ''){
                    $donationAddress->setZipCode($addressFormZipCode);
                }
                // Sinon je remplis le tableau d'erreur
                else {
                    $this->addFlash('warning', 'Veuillez renseigner un code postal');
                    $errorList['zipCode'] = 'Veuillez renseigner le code postal';
                }
                // Si city pas vide alors je sette
                if(trim($addressFormCity) != ''){
                    $donationAddress->setCity($addressFormCity);
                }
                // Sinon je remplis le tableau d'erreur
                else{
                    $this->addFlash('warning', 'Veuillez renseigner un nom de ville');
                    $errorList['city'] = 'Veuillez renseigner le nom de la ville';
                }
                // Si mon tableau d'erreur est vide alors je peux enregistrer en base de données
                // Le tableau me permet uniquement de vérifier si il y a des erreurs
                if(count($errorList) == 0){
                    //on persist la nouvelle adresse
                    $em->persist($donationAddress);
                    // on attribue la nouvelle addresse au don
                    $donation->setAddress($donationAddress);
                }
                // Sinon je signale les erreur
                else {
                    return $this->render('donation/new.html.twig', [
                        'form' => $form->createView()
                    ]); 
                }
            }
            // Je persist tous les produits
            foreach($donation->getProducts() as $product){
                if($product->getExpiryDate() == null){
                    $this->addFlash('danger', 'Veuillez renseigner la date d\'expiration');
                    return $this->redirectToRoute('donation_new');
                }
                if($product->getName() == null){
                    $this->addFlash('danger', 'Veuillez renseigner le nom du produit');
                    return $this->redirectToRoute('donation_new');
                }

                $em->persist($product);
            }
            // Pour setter le giver je récupere le currentUser
            $user = $this->getUser();
            $donation->addUser($user);
            $currentPoints = $user->getPoints();
            $newPoints = $currentPoints + 5;
            $user->setPoints($newPoints);           
            // on utilise rewarder pour metre à jour (si besoin) le reward
            $reward = $rewarder->rewarder($newPoints);
            $user->setReward($reward);
            // Je vérifie qu'il y ait au moins un produit dans le don.
            $data = $form->getData();
            if(count($data->getProducts()) == 0){
                $this->addFlash('danger', 'Veuillez ajouter au moins un produit');
                return $this->render('donation/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
            // Je persist la donation
            $em->persist($donation);
            // J'effectue toutes les insertions en bdd
            $em->flush();
            // J'ajoute un flashMessage pour indiquer que tout s'est bien passé
            $this->addFlash('success', 'Le don a bien été publié !');

            //envoi d'un e-mail aux associations situées dans le département du nouveau don
            // 1 / récupérer le code postal du don
            $department = strval($donation->getAddress()->getZipCode());
            $splitDepartment = str_split($department, 2);
            $shortDepartment = $splitDepartment[0] . '%';
            // 2/ récupérer la collection des associations situées dans le département
            $associations = $userRepo->findUserByZipCode($shortDepartment);
            // 3/ envoyer l'e-mail
            // on boucle sur la collection d'association
            foreach($associations as $asso) {

                $email = $asso->getEmail(); // Déclaration de l'adresse de destination.
                        $firstName = $asso->getFirstName();
                        $lastName = $asso->getLastName();         
                        $mail = (new \Swift_Message('Un nouveau don a été publié dans votre département'))
                        ->setFrom('ofoodbank@gmail.com')
                        ->setTo($email)
                        ->setBody(
                                $this->renderView(
                                    'mailer/mail-new-donation-in-area.html.twig',
                                    [
                                        'email' => $email,
                                        'donationTitle' => $donation->getTitle(),
                                        'donationId' => $donation->getId(),
                                        'donationCity' =>$donation->getAddress()->getCity(),
                                        'donationZipCode'=>$department,
                                        'firstName' => $firstName,
                                        'lastName' => $lastName
                                    ]
                                ),
                                'text/html'
                            );                            
                    $mailer->send($mail);
            }

            // Je retourne a la liste des tags
            return $this->redirectToRoute('donation_list');
        }
        
        return $this->render('donation/new.html.twig', [
            'form' => $form->createView()
        ]); 
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

    // Ajouter au fur et a mesure dans la base de données
    // A la fin de l'ajout des produits
    // Je récupere les id des produits

    //////////////////Systeme de notation////////////////////////////
    //Il va nous falloir une nouvelle route /donation/{id}/votes
    /**
     * @Route("/{id}/vote", name="vote")
     */
    public function addVoteOnUser($id, Request $request, EntityManagerInterface $em, Donation $donation, UserRepository $userRepo){
        // On récupere le vote saisi par l'utilisateur
        $userVote = $request->request->get('stars');
        dump($userVote);
        if($donation->getIsVoted() == null){
            // On récupere l'utilisateur
            $user = $userRepo->findUserDonationByRole($id);
            $donationUser = $user[0];
            /*Si cette note vaut null alors cela signifie que le donateur n'a pas encore eu de notes
            Donc la note BDD Donateur vaudra celle saisie par l'utilisateur*/
            if($donationUser->getRating() == null){
                $donationUser->setRating($userVote); 
                $donation->setIsVoted(1);
            }
            // Si elle ne vaut pas null alors le donateur a déjà été noté
            //On va donc faire une moyenne
            //Note en BDD = ( Note en BDD + vote du jour ) / 2
            else {
                $donationUser->setRating(($donationUser->getRating()+$userVote)/2);
                $donation->setIsVoted(1);
            }
            
            //On push ensuite cette valeur en base de donnée pour pouvoir la réutiliser
            $em->flush();
        }
        return $this->redirectToRoute('donation_show', [
            'id' => $id
        ]);        
    }  
}

