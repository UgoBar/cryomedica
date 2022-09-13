<?php

namespace App\Controller;

use App\Entity\CryoHistoric;
use App\Entity\CryoMedia;
use App\Form\CryoHistoricType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoricController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/historic', name: 'back_historic')]
    public function list(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $flashbag = $this->helper->getFlashBag() ?? false;

            $historic = $this->helper->em->getRepository(CryoHistoric::class)->findBy(
                [],
                ['date' => 'ASC']
            ) ?? false;


            return $this->render('back/historic/list.html.twig', [
                'nav' => 'historic',
                'title' => 'Historique',
                'historic' => $historic,
                'flashbag' => $flashbag,
            ]);
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/historic/ajout', name: 'create_historic')]
    #[Route('/admin/historic/edit/{id}', name: 'edit_historic')]
    public function form(Request $request, CryoHistoric $historic = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$historic) {
                $media = new CryoMedia();
                $historic = new CryoHistoric();
                $editmode = false;
            } else {
                $editmode = true;
                $media = $historic->getMedia();
            }

            $form = $this->createForm(CryoHistoricType::class, $historic);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record Historic with previously created Media
                $historic->setText($data->getText());
                $historic->setDate($data->getDate());
                $historic->setTitle($data->getTitle());
                $historic->setMedia($media);
                $this->helper->em->persist($historic);
                // Flush in database
                $this->helper->em->flush();

                // Redirect and add flashbag
                $flashbagText = $editmode ? 'modifié' : 'enregistré';
                $this->helper->addFlashBag("L'historique a bien été $flashbagText");
                return $this->redirectToRoute('back_historic');
            }

            return $this->render('back/historic/form.html.twig', [
                'nav' => 'historic',
                'title' => 'Ajout d\'un historique',
                'editMode' => $editmode,
                'media' => $media,
                'historic' => $historic,
                'form' => $form->createView(),
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/historic/delete', name: 'delete_historic')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $historicId = (int)$request->request->all()['id'];
            $historic = $this->helper->em->getRepository(CryoHistoric::class)->find($historicId);
            $this->helper->em->remove($historic);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }
}
