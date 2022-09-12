<?php


namespace App\Controller;


use App\Entity\CryoUser;
use App\Form\Users\CryoUserPasswordType;
use App\Form\Users\CryoUserType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/users', name: 'users')]
    public function dashboard(): Response
    {

        if($this->helper->verifyConnection()) {

            $users = $this->helper->em->getRepository(CryoUser::class)->findAll();
            $flashbag = $this->helper->getFlashBag() ?? false;

            return $this->render('back/users/list.html.twig', [
                'nav' => 'users',
                'title' => 'Utilisateurs',
                'users' => $users,
                'flashbag' => $flashbag
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/user/ajout', name: 'create_user')]
    public function form(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $user = new CryoUser();
            $user->setEmail('');

            $passwordError = false;

            $form = $this->createForm(CryoUserType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                if($data->getPassword() === $data->getConfirmPassword()) {
                    $user->setFirstname($data->getFirstname());
                    $user->setLastname($data->getLastname());
                    $user->setEmail($data->getEmail());
                    $user->setRole($data->getRole());

                    $passwordHash = password_hash($data->getPassword(), PASSWORD_DEFAULT);
                    $user->setPassword($passwordHash);

                    $this->helper->em->persist($user);
                    $this->helper->em->flush();

                    // Redirect and add flashbag
                    $this->helper->addFlashBag("L'utilisateur a bien été ajouté");
                    return $this->redirectToRoute('users');
                }

                $passwordError = 'Les deux mots de passes ne sont pas les mêmes';
            }

            return $this->render('back/users/form.html.twig', [
                'nav' => 'users',
                'title' => 'Ajout d\'un utilisateur',
                'form' => $form->createView(),
                'error' => $passwordError
            ]);

        }
        return $this->redirectToRoute('login');
    }



    #[Route('/admin/user/{id}/update-password', 'update_user_password')]
    public function updatePassword(Request $request, CryoUser $user): RedirectResponse|Response
    {
        $passwordError = false;

        if($this->helper->verifyConnection() && $user) {

            $form = $this->createForm(CryoUserPasswordType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                if($data->getPassword() === $data->getConfirmPassword()) {
                    $passwordHash = password_hash($data->getPassword(), PASSWORD_DEFAULT);
                    $user->setPassword($passwordHash);

                    $this->helper->em->persist($user);
                    $this->helper->em->flush();

                    // Redirect and add flashbag
                    $this->helper->addFlashBag("Le mot de passe a bien été modifié");
                    return $this->redirectToRoute('users');
                }

                $passwordError = 'Les deux mots de passes ne sont pas les mêmes';

            }

            return $this->render('back/users/update_password.html.twig', [
                'nav' => 'users',
                'title' => 'Ajout d\'un utilisateur',
                'form' => $form->createView(),
                'error' => $passwordError
            ]);
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/user/delete', name: 'delete_user')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $userId = (int)$request->request->all()['id'];
            $userRepo = $this->helper->em->getRepository(CryoUser::class);
            $user = $userRepo->find($userId);
            $this->helper->em->remove($user);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }
}
