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
        // on récupère la collection de user afin d'identifier le donateur
        $users = $donation->getUsers();
        // pour chaque utilisateur
        foreach ($users as $user){
            // dump($user->getRoles());
            // on récupère le tableau de rôle et on boucle dessus
            foreach ($user->getRoles() as $role){
                // dump($role);
                // si le rôle est 'ROLE_ASSOC', on identifie l'utilisateur
                if ('ROLE_ASSOC' == $role){
                    $collector = $user;
                } else { //
                    $collector = null;
                }
                // si le rôle est 'ROLE_GIVER', on identifie l'utilisateur comme étant le donateur
                if ('ROLE_GIVER' == $role){
                    $giver = $user;
                }
            }
        }
        // dump($collector);
        // dump($giver);
        // die;
        // dd(giver);

        // dump($donation->getUsers());

        return $this->render('donation/show.html.twig', [
            'donation' => $donation,
            'giver' => $giver,
            'collector' => $collector,
        ]);
    }

    /**
     * @Route("/{id}/select", name="select", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function select(Donation $donation, StatusRepository $statusRepository, EntityManagerInterface $em)
    {
        // on crée un nouvel objet Status 
        $newStatus = $statusRepository->findOneByName('Réservé');
        // dd($newStatus);
        // on change le status de la donnation
        $donation->setStatus($newStatus);
        // on ajoute l'id du demandeur à la donnation
        $donation->addUser($this->getUser());
        // on persist et on flush
        $em->persist($donation);
        $em->flush();

        // on crée la variable "collector" à qui on attribue l'utilisateur courant
        $collector = $this->getUser();

        dump($donation->getUsers());

        // ajout d'un flash message
        $this->addFlash(
            'success',
            'La demande de réservation est bien prise en compte'
        );

        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
            // 'giver' => $donation->getUsers()[0],
            // 'collector'=> $collector,
        ]);

    }

    /**
     * @Route("/{id}/deselect", name="deselect", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function deselect(Donation $donation, EntityManagerInterface $em, StatusRepository $statusRepository)
    {
        // on crée un nouvel objet Status
        $newStatus = $statusRepository->findOneByName('Dispo');
        // on attribue le status au don
        $donation->setStatus($newStatus);
        // on retire l'id du user 
        $donation->removeUser($this->getUser());
        // on persist et on flush
        $em->persist($donation);
        $em->flush();

        // ajout d'un Flash Message
        $this->addFlash(
            'success',
            'Vous avez bien annulé la réservaton de ce don'
        );

        dump($donation->getUsers());

        return $this->redirectToRoute('donation_show', [
            'donation' => $donation,
            'id' => $donation->getId(),
            // 'giver' => $donation->getUsers()[0]
        ]);
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
