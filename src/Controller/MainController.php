<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/informations", name="main_informations")
     */
    public function informations()
    {
        return $this->render('main/informations.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/contact", name="main_contact")
     */
    public function contact()
    {
        return $this->render('main/contact.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/legal-mentions", name="main_legalMentions")
     */
    public function legalMentions()
    {
        return $this->render('main/legal-mentions.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/about", name="main_about")
     */
    public function about()
    {
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
