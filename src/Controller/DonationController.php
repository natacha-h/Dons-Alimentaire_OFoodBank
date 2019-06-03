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
use App\Repository\StatusRepository;

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
    public function new(Request $request, CategoryRepository $cateRepo, EntityManagerInterface $em, StatusRepository $StatusRepo)
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Je lui fournis un status disponible directement
            $status = $StatusRepo->findOneByName('Dispo');
            $donation->setStatus($status);

            // Je persist l'adresse
            $em->persist($donation->getAddress());

            // Je persist tous les produits
            foreach($donation->getProducts() as $product){
                $em->persist($product);
            }

            // Je persist la donation
            $em->persist($donation);

            // J'effectue toutes les insertions en bdd
            $em->flush();

        }

        return $this->render('donation/new.html.twig', [
            'form' => $form->createView()
        ]); 
    }

}

// Ajouter au fur et a mesure dans la base de données
// A la fin de l'ajout des produits
// Je récupere les id des produits