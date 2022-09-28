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

            $contacts = $this->helper->em->getRepository(CryoContact::class)->findBy(
                    [],
                    ['createdAt' => 'DESC']
                ) ?? false;

            return $this->render('back/contact/list.html.twig', [
                'nav' => 'contacts',
                'title' => 'Liste des contact',
                'contacts' => $contacts,
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
        return new JsonResponse(['actionResponse'=>'unauthorized', 401]);

    }

    #[Route('/admin/contact/export', name: 'export_contact')]
    public function exportContact(): JsonResponse | Response
    {
        if($this->helper->verifyConnection()) {

            $contacts = $this->helper->em->getRepository(CryoContact::class)->getContactAsArray() ?? false;

            if($contacts) {
                // Column
                $rows[] = implode(',', ['prénom', 'nom', 'email', 'tel', 'nom du centre', 'ville', 'statut (1=ouvert)']);

                // Rows
                foreach ($contacts as $contact) {
                    $rows[] = implode(',', $contact);
                }

                // Convert array to string for CSV conversion
                $content = implode("\n", $rows);

                // Create response and return it
                $response = new Response($content);
                $response->headers->set('Content-Type', 'text/csv');
                $this->helper->addFlashBag('download_contact', true);
                return $response;
            }
            return new JsonResponse(['error' => 'Aucun contact trouvé', 404]);
        }
        return new JsonResponse(['error' => 'Unauthorized', 401]);
    }
}

