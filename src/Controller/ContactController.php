<?php


namespace App\Controller;


use App\Entity\CryoCenters;
use App\Entity\CryoContact;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/contact', name: 'contacts')]
    public function contact(Request $request): Response
    {
        if ($this->helper->verifyConnection()) {

            if($commitment = $request->query->get('commitment')) {
                $contacts = $this->helper->em->getRepository(CryoContact::class)->findBy(
                    ['commitment' => $commitment],
                    ['createdAt' => 'DESC']
                ) ?? false;
                $subnav = $commitment;
            } else {
                $contacts = $this->helper->em->getRepository(CryoContact::class)->findBy(
                    [],
                    ['createdAt' => 'DESC']
                ) ?? false;
                $subnav = 'all';
            }

            return $this->render('back/contact/list.html.twig', [
                'nav' => 'contacts',
                'title' => 'Liste des contact',
                'contacts' => $contacts,
                'subnav' => $subnav
            ]);
        }

        return $this->redirectToRoute('login');
    }

    #[Route('/admin/contact/delete', name: 'delete_contact')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $contactId = (int)$request->request->all()['id'];
            $contact = $this->helper->em->getRepository(CryoContact::class)->find($contactId);
            $this->helper->em->remove($contact);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);

    }
}
