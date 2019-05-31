<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/register", name="inscription")
     */
    public function register()
    {
        return $this->render('user/register.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show(User $user)
    {   
        
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    
    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit()
    {
        // POST
    }
  
}
