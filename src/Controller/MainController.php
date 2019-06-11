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
    public function contact(Request $request, \Swift_Mailer $mailer)

    //Environment pour pourvoir afficher un email au format HTML et $renderer partie TWIG
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

        // Créer un tableau vide stockant les erreurs
        $arrayErrors = [];

        // Vérifier si le firstname est vide
        // Si vide
        // Alors ajouter un message d'erreur dans le tableau
        if(empty($firstname)){
            $arrayErrors['firstname'] = 'Veuillez renseigner votre prénom';

        }
        if(empty($lastname)){
            $arrayErrors['lastname'] = 'Veuillez renseigner votre nom';

        }
        if(empty($email)){
            $arrayErrors['email'] = 'Veuillez renseigner votre email';

        }
        if(empty($need)){
            $arrayErrors['need'] = 'Veuillez renseigner votre besoin';

        }
        if(empty($message)){
            $arrayErrors['message'] = 'Veuillez renseigner votre message';

        }

        //dump($arrayErrors);
        dump($email);
        // Si tableau d'erreur vide alors envoyer le mail

        if(count($arrayErrors) == 0){

           $mail = (new \Swift_Message($need))
               ->setFrom($email)
               ->setTo('ofoodbank@gmail.com')
               ->setBody(
                    $this->renderView(
                        'mailer/mail.html.twig',
                        [
                            'message' => $message,
                            'email' => $email,
                            'need' => $need,
                            'firstname' => $firstname,
                            'lastname' => $lastname
                        ]
                    ),
                    'text/html'
                );
                    
                
           $mailer->send($mail);
            

            /*
             * ->setBody(
            $this->renderView(
                // templates/emails/registration.html.twig
                'emails/registration.html.twig',
                ['name' => $name]
            ),
            'text/html'
        )
             */
            // Ajout flashMessage    
    
        }

        return $this->render('main/contact.html.twig', [
            
            ]);
 
       
        
        
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
