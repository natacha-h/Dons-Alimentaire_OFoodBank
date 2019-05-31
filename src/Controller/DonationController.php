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
        $product->setName('Lustucru');
        $product->setQuantity(1);
        $product->setDescription('des pates');
        $product->setExpiryDate(new \DateTime());
        $product->setCategory($cateRepo->findOneById(25));

        // $address = new Address();
        // $address->setNumber(5);
        // $address->setStreet('rue de la paix');
        // $address->setZipCode(75000);
        // $address->setCity('Paris');
        // $em->persist($address);

        $donation->addProduct($product);
        // $donation->setAddress($address);

        $formDon = $this->createForm(DonationType::class, $donation);

        $formDon->handleRequest($request);
        return $this->render('donation/new.html.twig', [
            'formDon' => $formDon->createView()
        ]); 
    }

    /**
     * @Route("/new/ajax", name="new_ajax", methods={"POST"})
     */
    public function newAjax(Request $request)
    {
        if($request->isXmlHttpRequest()){
                $donation = $request->request->get('donation');

                return $this->json(1);
            } else {
                return $this->createAccessDeniedException('methode non autorisée');
            }
        }
}

// Ajouter au fur et a mesure dans la base de données
// A la fin de l'ajout des produits
// Je récupere les id des produits