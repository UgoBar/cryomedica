<?php

namespace App\Controller;

use App\Entity\CryoUser;
use App\Helper\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $mailTo = '';
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

        return $this->render('login/login.html.twig', [
            'email' => $email,
            'mailTo' => $mailTo,
            'loginError' => $loginError
        ]);
    }

    #[Route('/admin/login/sendmail', name: 'send_mail_password')]
    public function sendUpdatePassword(Request $request): JsonResponse
    {
        if($mailTo = $request->request->all()['email']) {

            if(!filter_var($mailTo, FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(['response' => 'Format de l\'email invalide'], 404);
            }

            $userRepo = $this->em->getRepository(CryoUser::class);
            $requestedUser = $userRepo->findOneBy(['email' => $mailTo]);

            if($requestedUser) {
                // Create token
                $token = bin2hex(random_bytes(20));

                // send mail
                $subject = "Reinitialisation du mot de passe";
                $msg = '<html><body>';
                $msg .= "Pour réinitialiser ton mot de passe cliques sur ce lien : https://www.cryomedica.fr/admin/update-password/form?token=$token";
                $msg .= "<br>Il faut absolument faire la démarche avec le même navigateur que celui avec lequel tu as fais la demande de réinitialisation de mot de passe.";
                $msg .= '</body></html>';
                $msg = wordwrap($msg,70);

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $headers .= 'From: Cryomedica <contact@cryomedica.fr>'."\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();

                mail($mailTo, $subject, $msg, $headers);
                $this->helper->session->set('token', $token);
                $this->helper->session->set('userMail', $mailTo);
                return new JsonResponse(['token'=> $token]);
            }
        }
        return new JsonResponse(['response'=>'Aucun email envoyé'], 401);
    }

    #[Route('/admin/update-password/form', name: 'update_password_form')]
    public function updateForm(Request $request): Response
    {
        if(!$request->query->get('token')) {
            return $this->redirectToRoute('login');
        }

        $email = $this->helper->session->get('userMail') ?? '';

        return $this->render('login/update_password.html.twig', [
            'email' => $email
        ]);
    }

    #[Route('/admin/update-password/action', name: 'update_password_action')]
    public function updatePassword(Request $request)
    {
        $email = $request->request->all()['email'];
        $password = $request->request->all()['password'];
        $confirmPassword = $request->request->all()['confirmPassword'];
        $urlToken = $request->query->get('token');
        $sessionToken = $_SESSION['token'];

        if($email && $password && $confirmPassword && $urlToken) {

            // Valide mail ?
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(['error' => 'Format de mail invalide'], 404);
            }

            // Mail in database ?
            $userRepo = $this->em->getRepository(CryoUser::class);
            if(!$requestedUser = $userRepo->findOneBy(['email' => $email])) {
                return new JsonResponse(['error' => 'Mail inconnu en base'], 401);
            }

            // Same passwords ?
            if($password !== $confirmPassword) {
                return new JsonResponse(['error' => 'Les deux mots de passe ne sont pas les mêmes'], 404);
            }

            if(!$sessionToken ) {
                return new JsonResponse(['error' => 'Le lien a expiré, il faut refaire une demande de réinitialisation depuis le login et cliquer sur le <b>dernier</b> mail'], 404);
            }

            // Tokens similar ?
            if($urlToken !== $sessionToken) {
                return new JsonResponse(['error' => 'Mauvais lien, il faut refaire une demande de réinitialisation depuis le login et cliquer sur le <b>dernier</b> mail'], 404);
            }

            // Hash and Update password and send success response
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $requestedUser->setPassword($passwordHash);
            $this->em->persist($requestedUser);
            $this->em->flush();
            return new JsonResponse(['response'=>'success']);

        } else {
            return new JsonResponse(['error' => 'Il manque des paramètres, tous les champs n\'ont pas été rempli'], 404);
        }
    }
}
