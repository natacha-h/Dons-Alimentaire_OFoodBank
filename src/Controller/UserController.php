<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/register", name="inscription", methods={"GET","POST"})
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request); //ATTENTION cette methode met à jour la variable $form + $user

        if ($form->isSubmitted() && $form->isValid()) {

            //cette methode issue de la classe UserPasswordEncoderInterface permet d'encoder le mot de passe par rapport au configuration appliquée sur l'objet fournit
            $hash = $passwordEncoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $em->persist($user->getAddress());

            $em->persist($user);
            $em->flush();
            
            $this->addFlash('success', 'Votre profil a bien été créé.');

            return $this->redirectToRoute('main_index');
        }

        return $this->render('user/register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"}, methods={"GET"})
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
