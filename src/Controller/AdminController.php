<?php

namespace App\Controller;

use App\Entity\CryoUser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    private \Doctrine\Persistence\ObjectManager $em;
    protected \Symfony\Component\HttpFoundation\Session\SessionInterface $session;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->em = $doctrine->getManager();
        $this->session = $requestStack->getSession();
    }

    #[Route('/admin/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {

        if($this->verifyConnection()) {

            return $this->render('back/dashboard.html.twig', [
                'title' => 'Dashboard'
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/banner', name: 'banner')]
    public function banner(): Response
    {
        if($this->verifyConnection()) {

            return $this->render('back/banner.html.twig', [
                'title' => 'Banner'
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/logout', name: 'logout')]
    public function logout(): Response
    {
        $this->session->set('connected', false);
        $this->session->set('user', false);
        return $this->redirectToRoute('login');
    }


    public function verifyConnection(): bool
    {
        // Is user defined and session and type of User ?
        $user = ($this->session->get('user') && $this->session->get('user') instanceof CryoUser) ? $this->session->get('user') : false;
        // Is 'connected' is defined in session ?
        $connected = $this->session->get('connected') ?? false;

        if ($connected && $user) {
            return true;
        }

        return false;
    }

    /** Add a flashbag
     * @param string $texte le message a afficher
     * @param string $level le niveau du message (correspond au type d'info bulle boostrap : success - warning - danger ...)
     */
    public function addFlashBag(string $texte, string $level = 'success'): static
    {
        if (!$this->session->get('flashbag') !== null || !is_array($this->session->get('flashbag')))
            $this->session->set('flashbag', []);

        $this->session->set('flashbag', ['message' => $texte, 'level' => $level]);

        return $this;
    }

    /** Get flashbag(s) */
    public function getFlashBag(): bool|array
    {
        if ($this->session->get('flashbag') !== null && is_array($this->session->get('flashbag'))) {
            $flashbags = $this->session->get('flashbag');
            $this->session->set('flashbag', false);
            return $flashbags;
        }
        return false;
    }
}
