<?php

namespace App\Controller;

use App\Entity\CryoUser;
use App\Helper\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

    private \Doctrine\Persistence\ObjectManager $em;
    private Helper $helper;

    public function __construct(ManagerRegistry $doctrine, Helper $helper)
    {
        $this->em = $doctrine->getManager();
        $this->helper = $helper;
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

                $this->helper->session->set('connected', true);
                $this->helper->session->set('user', $requestedUser);
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
