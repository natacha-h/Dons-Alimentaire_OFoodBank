<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

/**
 * @Route("/manage/user", name="admin_")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("", name="manage")
     */
    public function manage(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();


        return $this->render('Backend/admin/manage.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/{id}", name="edit")
     */
    public function edit()
    {
        return $this->render('Backend/admin/edit.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
