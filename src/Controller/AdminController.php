<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function login(): Response
    {
        return $this->render('back/index.html.twig', [
            'controller_name' => 'Dashboard',
        ]);
    }
}
