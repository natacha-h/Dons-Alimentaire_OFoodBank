<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\DonationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DonationRepository $donationRepo)
    {
    
        $donations = $donationRepo->findBy([], ['created_at' => 'DESC'],3);

        return $this->render('main/index.html.twig', [
            'donations'=> $donations
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
            
        ]);
    }
}
