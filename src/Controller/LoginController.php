<?php

namespace App\Controller;

use App\Entity\CryoUser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

    private \Symfony\Component\HttpFoundation\Session\SessionInterface $session;
    private \Doctrine\Persistence\ObjectManager $em;

    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
        $this->em = $doctrine->getManager();
        $this->session = $requestStack->getSession();
    }

    #[Route('/admin/login', name: 'login')]
    public function index(): Response
    {
        $loginError = false;
        $email = '';
        $requestedUser = false;

        // When login form is called
        if(isset($_POST['email'])) {

            // Validate email
            if($_POST['email'])
            {
                if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $email = $_POST['email'];
                    $userRepo = $this->em->getRepository(CryoUser::class);
                    $requestedUser = $userRepo->findOneBy(['email' => $email]);
                }
                else
                    $loginError = true;
            } else
                $loginError = true;

            // Validate password
            if($_POST['password'] && $requestedUser) {
                $password = $_POST['password'];
                $hash = substr( $requestedUser->getPassword(), 0, 60 );
            } else
                $loginError = true;

            if($requestedUser && password_verify($password, $hash)) {

                $this->session->set('connected', true);
                $this->session->set('user', $requestedUser);
                return $this->redirectToRoute('dashboard');
            } else
                $loginError = true;
        }

        return $this->render('login/index.html.twig', [
            'email' => $email,
            'loginError' => $loginError
        ]);
    }
}
