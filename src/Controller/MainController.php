<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/informations", name="informations")
     */
    public function informations()
    {

        return $this->render('main/informations.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/contact", name="contact", methods={"post", "get"})
     */
    public function contact(Request $request)

    {
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $email = $request->request->get('email');
        $need = $request->request->get('need');
        $message = $request->request->get('message');
        //dump($firstname);
        //dump($lastname);
        //dump($email);
        //dump($message);
 
        if (filter_var($email, FILTER_VALIDATE_EMAIL)){
        }
        return $this->render('main/contact.html.twig');
        
    }


    /**
     * @Route("/legal-mentions", name="legalMentions")
     */
    public function legalMentions()
    {
        return $this->render('main/legal-mentions.html.twig', [
            
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('main/about.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
