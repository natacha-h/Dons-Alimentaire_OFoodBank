<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Repository\DonationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dons", name="donation_")
 */

class DonationController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function list(DonationRepository $donationRepository)
    {
        // $repo = $this->getDoctrine()->getRepository(Donation::class);

        $donations = $donationRepository->findAll();
        
        $expiryDateArray = [];
        foreach($donations as $donation){
            $currentExpiry = null;
            dd($donation);
            foreach($donation->getProducts() as $product){
                $expiryDate = $product->getExpiryDate();
        
                // Premier tour de boucle on récupere le current
                if($currentExpiry == null){
                    $currentExpiry = $expiryDate;
                }
                
                // On transforme les dates en secondes UNIX
                // Secondes écoulées depuis 1 janvier 1970
                $currentExpiry = time($currentExpiry);
                $expiryDate = time($expiryDate);
        
                // Nombre de secondes écoulées plus faible <=> date plus proche
                if($currentExpiry >= $expiryDate){
                    $currentExpiry = $expiryDate;
                }
            }
        
            // On retransforme la date en date lisible
            $expiryDate = date('d/m/Y', $currentExpiry);
            $donation[] = $expiryDate;
        }

        

        return $this->render('donation/list.html.twig', [
            'donations' => $donations,
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show()
    {
        return $this->render('donation/show.html.twig', [
            
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

