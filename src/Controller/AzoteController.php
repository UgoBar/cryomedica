<?php

namespace App\Controller;

use App\Entity\CryoMedia;
use App\Entity\CryoAzote;
use App\Form\CryoAzoteType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AzoteController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/azote', name: 'azote')]
    public function list(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $flashbag = $this->helper->getFlashBag() ?? false;

            $azote = $this->helper->em->getRepository(CryoAzote::class)->findBy(
                [],
                ['position' => 'ASC']
            ) ?? false;

            if(isset($_POST['order'])) {
                foreach ($azote as $cabin) {
                    $cabin->setPosition($_POST['azote-'.$cabin->getId().'-position']);
                    $this->helper->em->persist($cabin);
                }
                $this->helper->em->flush();

                // Redirect and add flashbag
                $this->helper->addFlashBag("L'ordre des cabines à l'azote a bien été modifié");
                return $this->redirectToRoute('azote');
            }

            return $this->render('back/azote/list.html.twig', [
                'nav' => 'azote',
                'title' => 'Cabines à l\'azote',
                'azote' => $azote,
                'flashbag' => $flashbag,
            ]);
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/azote/ajout', name: 'create_azote')]
    #[Route('/admin/azote/edit/{id}', name: 'edit_azote')]
    public function form(Request $request, CryoAzote $cryoAzote = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$cryoAzote) {
                $media = new CryoMedia();
                $cryoAzote = new CryoAzote();
                $editmode = false;
            } else {
                $editmode = true;
                $media = $cryoAzote->getMedia();
            }

            $form = $this->createForm(CryoAzoteType::class, $cryoAzote);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record CryoAzote with previously created Media
                $cryoAzote->setText($data->getText());
                $cryoAzote->setPosition($data->getPosition());
                $cryoAzote->setTitle($data->getTitle());
                $cryoAzote->setMedia($media);
                $this->helper->em->persist($cryoAzote);
                // Flush in database
                $this->helper->em->flush();

                // Redirect and add flashbag
                $flashbagText = $editmode ? 'modifiée' : 'enregistrée';
                $this->helper->addFlashBag("La cabine a bien été $flashbagText");
                return $this->redirectToRoute('azote');
            }

            return $this->render('back/azote/form.html.twig', [
                'nav' => 'azote',
                'title' => 'Ajout d\'une cabine à l\'azote',
                'editMode' => $editmode,
                'media' => $media,
                'testimony' => $cryoAzote,
                'form' => $form->createView(),
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/azote/delete', name: 'delete_azote')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $azoteId = (int)$request->request->all()['id'];
            $azote = $this->helper->em->getRepository(CryoAzote::class)->find($azoteId);
            $this->helper->em->remove($azote);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }
}
