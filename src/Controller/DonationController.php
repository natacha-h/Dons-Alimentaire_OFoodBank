<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Product;
use App\Utils\Rewarder;
use App\Entity\Donation;
use App\Form\ProductType;
use App\Form\DonationType;
use App\Repository\StatusRepository;
use Proxies\__CG__\App\Entity\Status;
use App\Repository\CategoryRepository;
use App\Repository\DonationRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function list(DonationRepository $donationRepository)
    {
        //repo = $this->getDoctrine()->getRepository(Donation::class);

        $donations = $donationRepository->findAll();
        
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

        

        return $this->render('donation/list.html.twig', [
            'donations' => $donations,
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
    public function select(Donation $donation, StatusRepository $statusRepository, EntityManagerInterface $em)
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
            'Vous avez bien annulé la réservaton de ce don'
        );

        dump($donation->getUsers());

        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
            // 'giver' => $donation->getUsers()[0]
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

                //je genere un nom de fichier unique pour eviter d'ecraser un fichier du meme nom & je concatene avec l'extension du fichier d'origine
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

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

}

// Ajouter au fur et a mesure dans la base de données
// A la fin de l'ajout des produits
// Je récupere les id des produits