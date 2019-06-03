<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Donation;
use Proxies\__CG__\App\Entity\Status;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/dons", name="donation_")
 */

class DonationController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list()
    {

        return $this->render('donation/list.html.twig', [
            'controller_name' => 'DonationController',
        ]);
    }

    /**
     * @Route("/{id}", name="show", requirements={"id"="\d+"})
     */
    public function show(Donation $donation)
    {
        $user = $donation->getUsers();
        // dd($user[0]);
        return $this->render('donation/show.html.twig', [
            'donation' => $donation,
            'user' => $user[0]
        ]);
    }

    /**
     * @Route("/{id}/select", name="select")
     */
    public function select(Donation $donation, StatusRepository $statusRepository, EntityManagerInterface $em)
    {
        // on crée un nouvel objet Status 
        $newStatus = $statusRepository->findOneByName('Réservé');
        // dd($newStatus);
        // on change le status de la donnation
        $donation->setStatus($newStatus);
        // on persist et on flush
        $em->persist($donation);
        $em->flush();

        // ajout d'un flash message
        $this->addFlash(
            'success',
            'La demande de réservation est bien prise en compte'
        );

        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
            'user' => $donation->getUsers()[0]
        ]);

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
