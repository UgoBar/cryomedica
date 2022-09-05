<?php


namespace App\Controller;


use App\Entity\CryoBio;
use App\Entity\CryoMedia;
use App\Entity\CryoUser;
use App\Form\CryoBioType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BioController extends AbstractController
{

    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/bio', name: 'bio')]
    public function form(Request $request): Response
    {

        if($this->helper->verifyConnection()) {

            $bio = $this->helper->em->getRepository(CryoBio::class)
                ->findAll()[0] ?? false;

            if (!$bio) {
                $media = new CryoMedia();
                $bio = new CryoBio();
                $editmode = false;
            } else {
                $editmode = true;
                $media = $bio->getMedia();
            }

            $form = $this->createForm(CryoBioType::class, $bio);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt() ?? '');
                $this->helper->em->persist($media);

                // Then record Picto with previously created Media
                $bio->setFirstText($data->getFirstText());
                $bio->setSecondText($data->getSecondText());
                $bio->setIsActive($data->isIsActive());
                $bio->setMedia($media);
                $this->helper->em->persist($bio);
                // Flush in database
                $this->helper->em->flush();
                // Redirect to the banner's list
                return $this->redirectToRoute('bio');
            }

            return $this->render('back/bio/form.html.twig', [
                'nav' => 'bio',
                'editMode' => $editmode,
                'title' => $editmode ? 'Modification de la présentation' : 'Ajout de la présentation',
                'media' => $media,
                'picto' => $bio,
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('login');
    }
}
