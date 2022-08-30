<?php

namespace App\Controller;

use App\Entity\CryoUser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

    private \Doctrine\Persistence\ObjectManager $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
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
                $_SESSION['connected'] = true;
                $_SESSION['user'] = $requestedUser;
                dump('Bien connecté !');exit;
            } else
                $loginError = true;
        }

        return $this->renderForm('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'email' => $email,
            'loginError' => $loginError
        ]);
    }
}
