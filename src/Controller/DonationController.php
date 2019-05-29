<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DonationController extends AbstractController
{
    /**
     * @Route("/dons", name="donation_list")
     */
    public function list()
    {
        return $this->render('donation/index.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }

    /**
     * @Route("/dons/{id}", name="donation_show")
     */
    public function show()
    {
        return $this->render('donation/show.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }

    /**
     * @Route("/dons/{id}/select", name="donation_select")
     */
    public function select()
    {
        // POST
    }

    /**
     * @Route("/dons/{id}/deselect", name="donation_deselect")
     */
    public function deselect()
    {
        // POST 
    }

    /**
     * @Route("/dons/new", name="donation_new")
     */
    public function new()
    {
        return $this->render('donation/new.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }
}
