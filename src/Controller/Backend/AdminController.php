<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manage/user", name="admin_")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("", name="manage")
     */
    public function manage()
    {
        return $this->render('Backend/admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/{id}", name="edit")
     */
    public function edit()
    {
        return $this->render('Backend/admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
