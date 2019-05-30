<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('donation/index.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }

    /**
     * @Route("/{id}", name="show")
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
     * @Route("/new", name="new")
     */
    public function new()
    {
        return $this->render('donation/new.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }
}
