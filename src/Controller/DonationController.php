<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Repository\DonationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/", name="donation_")
 */

class DonationController extends AbstractController
{
    /**
     * @Route("/dons", name="list", methods={"GET"})
     */
    public function list(DonationRepository $donationRepository)
    {
        // $repo = $this->getDoctrine()->getRepository(Donation::class);

        $donations = $donationRepository->findAll();

        return $this->render('donation/list.html.twig', [
            'donations' => $donations,
        
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show()
    {
        return $this->render('donation/show.html.twig', [
            
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
     * @Route("/new", name="new")
     */
    public function new()
    {
        return $this->render('donation/new.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }
}
