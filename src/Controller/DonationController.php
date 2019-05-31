<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Product;
use App\Entity\Donation;
use App\Form\ProductType;
use App\Form\DonationType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/dons", name="donation_")
 */
class DonationController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list()
    {

        return $this->render('donation/list.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show()
    {
        return $this->render('donation/show.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }

    /**
     * @Route("/{id}/select", name="select")
     */
    public function select()
    {
        // POST
    }

    /**
     * @Route("/{id}/deselect", name="deselect")
     */
    public function deselect()
    {
        // POST 
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     */
    public function new(Request $request, CategoryRepository $cateRepo, EntityManagerInterface $em)
    {
        $donation = new Donation();

        $product = new Product();
        $product->setName('');
        $product->setQuantity(1);
        $product->setDescription('');
        $product->setExpiryDate(new \DateTime());
        $product->setCategory($cateRepo->findOneById(25));

        $donation->addProduct($product);

        $formDon = $this->createForm(DonationType::class, $donation);

        $formDon->handleRequest($request);

        if ($formDon->isSubmitted() && $formDon->isValid()) {

            $em->persist($donation);
            $em->flush();
            return $this->redirectToRoute('dons_list');
        }

        return $this->render('donation/new.html.twig', [
            'formDon' => $formDon->createView()
        ]); 
    }

    /**
     * @Route("/new/ajax", name="new_ajax", methods={"POST"})
     */
    public function newAjax(Request $request, CategoryRepository $cateRepo, EntityManagerInterface $em)
    {
        if($request->isXmlHttpRequest()){
                $donation = $request->request->get('donation');
                // Je récupere les données
                $donationDecode = json_decode($donation, true);
                $donation = $donationDecode[0];

                $newDon = new Donation();
                $newDon->setTitle($donation['donationTitle']);
                $newDon->setImage($donation['donationPic']);

                $newAddress = new Address();
                $newAddress->setNumber($donation['address'][0]['number']);
                $newAddress->setStreet($donation['address'][0]['street']);
                $newAddress->setZipCode($donation['address'][0]['zipCode']);
                $newAddress->setCity($donation['address'][0]['city']);
                $em->persist($newAddress);
                $em->flush();

                $newDon->setAddress($newAddress);

                foreach($donation['products'][0] as $product){
                    $newProd = new Product();
                    $newProd->setName($product['productName']);
                    $newProd->setQuantity($product['productQuantity']);
                    $newProd->setDescription($product['productDescription']);

                    // Je récupere la categorie avec l'id de la catégorie
                    $category = $cateRepo->findOneBy($product['productCategory']);
                    // Je set la catégorie sur le produit
                    $newProd->setCategory($category);
                    $newProd->setExpiryDate(new \Datetime);
                    $em->persist($newProd);
                    $em->flush();
                    // J'ajoute le produit a la donation
                    $newDon->addProduct($product);
                }
                
                $em->persist($newDon);
                $em->flush();

                return $this->json(json_encode($donationDecode));
            } else {
                return $this->createAccessDeniedException('methode non autorisée');
            }
        }
}

// Ajouter au fur et a mesure dans la base de données
// A la fin de l'ajout des produits
// Je récupere les id des produits