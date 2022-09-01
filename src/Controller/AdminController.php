<?php

namespace App\Controller;

use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {

        if($this->helper->verifyConnection()) {

            return $this->render('back/dashboard.html.twig', [
                'nav' => 'dashboard',
                'title' => 'Dashboard'
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/logout', name: 'logout')]
    public function logout(): Response
    {
        $this->helper->session->set('connected', false);
        $this->helper->session->set('user', false);
        return $this->redirectToRoute('login');
    }
}
