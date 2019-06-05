<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function show(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {   
        //Je récupère l'ancien mot de passe
        $oldPassword = $user->getPassword();

        //active successivement les evenement PRE_SET_DATA et POST_SET_DATA
        $form = $this->createForm(UserType::class, $user);

        //active successivement PRE_SUBMIT , SUBMIT, POST_SUBMIT
        $form->handleRequest($request); //si je ne modifie pas mon password celui ci ecrase l'ancienne valeur par null
       
        if ($form->isSubmitted() && $form->isValid()) {

            //si ma valeur est nulle => je garde l'ancien mot de passe
            if(is_null($user->getPassword())){

                $encodedPassword = $oldPassword;

            //sinon je souhaite un nouveau mot de passe et je l'encode 
            } else {
                $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            }

            //dans tout les cas le stocke un password : nouveau ou ancien
            $user->setPassword($encodedPassword);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été effectuées'
            );

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
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
