<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="user_inscription")
     */
    public function register()
    {
        return $this->render('user/register.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/profil/{id}", name="user_show")
     */
    public function show()
    {
        return $this->render('user/show.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
    /**
     * @Route("/profil/{id}/edit", name="user_edit")
     */
    public function edit()
    {
        // POST
    }
  
}
