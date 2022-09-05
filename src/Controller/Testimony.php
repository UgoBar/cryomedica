<?php


namespace App\Controller;

use App\Entity\CryoMedia;
use App\Entity\CryoTestimony;
use App\Form\CryoTestimonyType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Testimony extends AbstractController
{

    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/temoignages', name: 'testimonials')]
    public function pictos(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $testimonials = $this->helper->em->getRepository(CryoTestimony::class)->findAll() ?? false;

            return $this->render('back/testimony/list.html.twig', [
                'nav' => 'testimonials',
                'title' => 'Liste des témoignages',
                'testimonials' => $testimonials,
            ]);
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/temoignage/ajout', name: 'create_testimonial')]
    #[Route('/admin/temoignage/edit/{id}', name: 'edit_testimonial')]
    public function form(Request $request, CryoTestimony $testimony = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$testimony) {
                $media = new CryoMedia();
                $testimony = new CryoTestimony();
            } else {
                $editmode = true;
                $media = $testimony->getMedia();
            }

            $form = $this->createForm(CryoTestimonyType::class, $testimony);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record Picto with previously created Media
                $testimony->setText($data->getText());
                $testimony->setSignature($data->getSignature());
                $testimony->setMedia($media);
                $this->helper->em->persist($testimony);
                // Flush in database
                $this->helper->em->flush();

                // Redirect to the banner's list
                return $this->redirectToRoute('testimonials');
            }

            return $this->render('back/testimony/form.html.twig', [
                'nav' => 'customers',
                'title' => 'Ajout d\'un client',
                'editMode' => $editmode ?? false,
                'media' => $media,
                'testimony' => $testimony,
                'form' => $form->createView(),
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/temoignage/delete', name: 'delete_testimonial')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $testimonyId = (int)$request->request->all()['id'];
            $testimony = $this->helper->em->getRepository(CryoTestimony::class)->find($testimonyId);
            $this->helper->em->remove($testimony);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);

    }
}
