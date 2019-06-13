<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Product;
use App\Utils\Rewarder;
use App\Entity\Donation;
use App\Utils\Addresser;
use App\Form\ProductType;
use App\Form\DonationType;
use App\Repository\StatusRepository;
use Proxies\__CG__\App\Entity\Status;
use App\Repository\CategoryRepository;
use App\Repository\DonationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\AddressRepository;

/**
 * @Route("/dons", name="donation_")
 */
class DonationController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(DonationRepository $donationRepository, PaginatorInterface $paginator, Request $request)
    {
        //repo = $this->getDoctrine()->getRepository(Donation::class);

        $donations = $donationRepository->findDonationWithProducts();

        $donationsList = $paginator->paginate(
            $donationRepository->findByStatusQuery(),
            $request->query->getInt('page', 1),
            10
        );

        // dd($donations);

        $expiryDateArray = [];
        foreach($donations as $donation){
            // dump($donation);
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

        // dump($expiryDateArray);
        // dump($donationsList);
        return $this->render('donation/list.html.twig', [
            'donations' => $donationsList,
            'expiryDateArray' => $expiryDateArray
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show(Donation $donation)
    {
        // on récupère la collection de user afin d'identifier le donateur
        $users = $donation->getUsers();
        // pour chaque utilisateur
        foreach ($users as $user){
            // dump($user->getRoles());
            // on récupère le tableau de rôle et on boucle dessus
            foreach ($user->getRoles() as $role){
                // dump($role);
                // si le rôle est 'ROLE_ASSOC', on identifie l'utilisateur
                if ('ROLE_ASSOC' == $role){
                    $collector = $user;
                } else { //
                    $collector = null;
                }
                // si le rôle est 'ROLE_GIVER', on identifie l'utilisateur comme étant le donateur
                if ('ROLE_GIVER' == $role){
                    $giver = $user;
                }
            }
        }
        // dump($collector);
        // dump($giver);
        // die;
        // dd(giver);

        // dump($donation->getUsers());

        return $this->render('donation/show.html.twig', [
            'donation' => $donation,
            'giver' => $giver,
            'collector' => $collector,
        ]);
    }

    /**
     * @Route("/{id}/select", name="select", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function select(Donation $donation, StatusRepository $statusRepository, EntityManagerInterface $em, Publisher $publisher)
    {
        // on crée un nouvel objet Status 
        $newStatus = $statusRepository->findOneByName('Réservé');
        // dd($newStatus);
        // on change le status de la donnation
        $donation->setStatus($newStatus);
        // on ajoute l'id du demandeur à la donnation
        $donation->addUser($this->getUser());
        // on persist et on flush
        $em->persist($donation);
        $em->flush();

        // on crée la variable "collector" à qui on attribue l'utilisateur courant
        $collector = $this->getUser();

        dump($donation->getUsers());

        // ajout d'un flash message
        $this->addFlash(
            'success',
            'La demande de réservation est bien prise en compte'
        );


        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
            // 'giver' => $donation->getUsers()[0],
            // 'collector'=> $collector,
        ]);

    }

    /**
     * @Route("/{id}/deselect", name="deselect", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function deselect(Donation $donation, EntityManagerInterface $em, StatusRepository $statusRepository)
    {
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

        dump($donation->getUsers());

        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
            // 'giver' => $donation->getUsers()[0]
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
        $zipCode = $address->getZipCode();
        $city = $address->getCity();
        $plussedCity = $addresser->addresser($city); 
        // On remplace les espaces du nom de la rue par des + grâce au service
        $street1 = $address->getStreet1();
        $plussedStreet = $addresser->addresser($street1);

        // On récupere le contenu de la page (retour json sur la page donc on recupere du json) avec ou sans numéro
        if($number != null){
            // On construit l'url avec les valeurs de la donation concernée avec chiffre
            $response = file_get_contents('https://nominatim.openstreetmap.org/search?q='.$number.'+'. $plussedStreet .',+'. $plussedCity .'&format=json&polygon=1&addressdetails=1&limit=1&countrycodes=fr&email=rpelletier86@gmail.com');
        } else{
            // On construit l'url avec les valeurs de la donation concernée sans chiffre
            $response = file_get_contents('https://nominatim.openstreetmap.org/search?q=' . $plussedStreet . ',+' . $plussedCity . '&format=json&polygon=1&addressdetails=1&limit=1&countrycodes=fr&email=rpelletier86@gmail.com');
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
    public function acceptDonation(StatusRepository $statusRepository, Donation $donation, EntityManagerInterface $em)
    {
        // on crée un nouvel objet Status 
        $newStatus = $statusRepository->findOneByName('Donné');
        // dd($newStatus);
        // on change le status de la donnation
        $donation->setStatus($newStatus);
        // on persist et on flush
        $em->persist($donation);
        $em->flush();

        // ajout d'un flash message
        $this->addFlash(
            'success',
            'Vous avez accepté la demande de l\'assocation, elle va être notifiée et prendra contact avec vous'
        );

        //TODO : NOTIFIER L'ASSO QUE SA DEMANDE EST ACCEPTÉE !!!!

        return $this->redirectToRoute('user_manage_donations', [
            'id' => $this->getUser()->getId(), // l'utilisateur courant est ici le donateur
        ]);
    }

    /**
     * @Route("/{id}/refuse", name="refuse", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function refuseDonation(Donation $donation, EntityManagerInterface $em, StatusRepository $statusRepository)
    {
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
            // dump($user->getRole()->getCode());

            //si le role de user est 'ROLE_ASSOC', on le donne en valeur de la variable $asso
            if ('ROLE_ASSOC' == $user->getRole()->getCode()){
                $asso = $user;
            }
        }
        // dd($asso);
        // on retire l'id de l'association
        $donation->removeUser($asso);
        // on persist et on flush
        $em->persist($donation);
        $em->flush();

        // ajout d'un Flash Message
        $this->addFlash(
            'success',
            'Vous avez refusé la demande de l\'association'
        );

        // dd($donation->getUsers());

        //TODO : NOTIFIER L'ASSOCIATION QUE SA DEMANDE EST REFUSÉE !!!!

        return $this->redirectToRoute('user_manage_donations', [
            'id' => $this->getUser()->getId(), // l'utilisateur courant est ici le donateur
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     */
    public function new(Request $request, CategoryRepository $cateRepo, EntityManagerInterface $em, StatusRepository $StatusRepo, Rewarder $rewarder)
    {
        $donation = new Donation();

        $product = new Product();
        $product->setName('');
        $product->setQuantity(1);
        $product->setDescription('');
        $product->setExpiryDate(new \DateTime());
        $product->setCategory($cateRepo->findOneById(64));
        $donation->addProduct($product);

        $donation->setCreatedAt(new \Datetime());
        $donation->setUpdatedAt(new \Datetime());

        $form = $this->createForm(DonationType::class, $donation);

        $addressFormNumber = $request->request->get('number');
        $addressFormStreet1 = $request->request->get('street1');
        $addressFormStreet2 = $request->request->get('street2');
        $addressFormZipCode = $request->request->get('zipCode');
        $addressFormCity = $request->request->get('city');

        $addressId = $request->request->get('index');

        $form->handleRequest($request);
        // dump($form->getData());
        
        if ($form->isSubmitted() && $form->isValid()) {
            //avant l'enregistrement d'un don je dois recuperer l'objet fichier qui n'est pas une chaine de caractere
            $file = $donation->getPicture();
            // dd($donation);
            if(!is_null($file)){

                $extension = $file->guessExtension();
                
                if($extension != 'jpg' | $extension != 'jpeg' | $extension != 'png' | $extension != 'gif' ){
                    $this->addFlash('danger', 'Le format de votre image ne correspond pas');

                    return $this->render('donation/new.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName().'.'.$extension;

                try {
                    //je deplace mon fichier dans le dossier souhaité
                    $file->move(
                        $this->getParameter('picture_directory'),
                        $fileName
                    );
                    
                } catch (FileException $e) {
                    dump($e);
                }
                $donation->setPicture($fileName);
            }
            // Je gere le fait de donner une image standard au don
            else {
                $fileName = 'default-image.jpg';
                $donation->setPicture($fileName);
            }

            // Je lui fournis un status disponible directement
            $status = $StatusRepo->findOneByName('Dispo');
            $donation->setStatus($status);

            // // Je persist l'adresse
            // $em->persist($donation->getAddress());
            
            // si les champs du formulaire sont vides, alors l'utilisateur a gardé l'adresse d'origine (= la sienne)
            if (null == $addressFormNumber && null == $addressFormStreet1 && null == $addressFormStreet2 && null == $addressFormZipCode && null == $addressFormCity) {
                // on attribue à la donation l'adresse du User/donateur
                ($donation->setAddress($this->getUser()->getAddress()));
            } else {// sinon c'est qu'il a choisi une autre adresse
                // on l'enregistre
                $donationAddress = new Address();
                $donationAddress->setNumber($addressFormNumber);
                $donationAddress->setStreet1($addressFormStreet1);
                $donationAddress->setStreet2($addressFormStreet2);
                $donationAddress->setZipCode($addressFormZipCode);
                $donationAddress->setCity($addressFormCity);
                //on persist la nouvelle adresse
                $em->persist($donationAddress);
                // on attribue la nouvelle addresse au don
                $donation->setAddress($donationAddress);
            }
            // dd($donation->getAddress());

            // Je persist tous les produits
            foreach($donation->getProducts() as $product){

                if($product->getExpiryDate() == null){
                    $this->addFlash('danger', 'Veuillez renseigner la date dexpiration');

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
    

}