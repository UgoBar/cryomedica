<?php

namespace App\Controller;

use App\Entity\CryoBanner;
use App\Entity\CryoMedia;
use App\Form\CryoBannerType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BannerController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/banners', name: 'banners')]
    public function banners(): Response
    {
        if($this->helper->verifyConnection()) {

            return $this->render('back/banner/list.html.twig', [
                'nav' => 'banner',
                'title' => 'Liste des bannières'
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/banner/ajout', name: 'create_banner')]
    #[Route('/admin/banner/edit/{id}', name: 'edit_banner')]
    public function form(Request $request, CryoBanner $banner = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$banner) {
                $media = new CryoMedia();
                $banner = new CryoBanner();
            } else {
                $editmode = true;
                $media = $banner->getMedia();
            }

            $form = $this->createForm(CryoBannerType::class, $banner);
//            dump($form);exit;

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record Banner with previously created media
                if($data->getPage() === 'home') {
                    $banner->setTitle($data->getTitle());
                    $banner->setSubtitle($data->getsubtitle());
                    $banner->setPosition($data->getPosition());
                }
                $banner->setPage($data->getPage());
                $banner->setMedia($media);
                $this->helper->em->persist($banner);
                // Flush in database
                $this->helper->em->flush();

                // Redirect to the banner's list
                return $this->redirectToRoute('banners');
            }

            return $this->render('back/banner/form.html.twig', [
                'nav' => 'banner',
                'title' => 'Ajout d\'une bannière',
                'form' => $form->createView(),
                'media' => $media,
                'banner' => $banner,
                'editMode' => $editmode ?? false,
            ]);

        }
        return $this->redirectToRoute('login');
    }
}
