<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function show(User $user, Request $request)
    {   
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été prises en compte'
            );
            
            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
                'user' => $user,
            ]);
        }
        return $this->render('user/show.html.twig', [
            'form' => $form->createView(),
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
