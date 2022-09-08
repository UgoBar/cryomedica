<?php

namespace App\Controller;

use App\Entity\CryoBanner;
use App\Entity\CryoMedia;
use App\Form\CryoBannerType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function banners(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $flashbag = $this->helper->getFlashBag() ?? false;

            $homeBanners = $this->helper->em->getRepository(CryoBanner::class)->findBy(
                ['page' => 'home'],
                ['position' => 'ASC']
            ) ?? false;

            $elecBanners = $this->helper->em->getRepository(CryoBanner::class)->findBy([
                    'page' => 'elec'
            ]) ?? false;

            $azoteBanners = $this->helper->em->getRepository(CryoBanner::class)->findBy([
                    'page' => 'azote'
            ]) ?? false;

            $aboutBanners = $this->helper->em->getRepository(CryoBanner::class)->findBy([
                    'page' => 'about'
            ]) ?? false;

            if(isset($_POST['order'])) {
                foreach ($homeBanners as $banner) {
                    $banner->setPosition($_POST['banner-'.$banner->getId().'-position']);
                    $this->helper->em->persist($banner);
                }
                $this->helper->em->flush();

                // Redirect and add flashbag
                $this->helper->addFlashBag("L'ordre des bannières a bien été modifié");
                return $this->redirectToRoute('banners');
            }

            return $this->render('back/banner/list.html.twig', [
                'nav' => 'banner',
                'title' => 'Liste des bannières',
                'homeBanners' => $homeBanners,
                'elecBanners' => $elecBanners,
                'azoteBanners' => $azoteBanners,
                'aboutBanners' => $aboutBanners,
                'flashbag' => $flashbag,
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/banner/delete', name: 'delete_banner')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $bannerId = (int)$request->request->all()['id'];
            $bannerRepo = $this->helper->em->getRepository(CryoBanner::class);
            $banner = $bannerRepo->find($bannerId);
            $this->helper->em->remove($banner);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }

    #[Route('/admin/banner/ajout', name: 'create_banner')]
    #[Route('/admin/banner/edit/{id}', name: 'edit_banner')]
    public function form(Request $request, CryoBanner $banner = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$banner) {
                $media = new CryoMedia();
                $banner = new CryoBanner();
                $editmode = false;
            } else {
                $editmode = true;
                $media = $banner->getMedia();
            }

            $form = $this->createForm(CryoBannerType::class, $banner);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record Banner with previously created Media
                if($data->getPage() === 'home') {
                    $banner->setTitle($data->getTitle());
                    $banner->setSubtitle($data->getSubtitle());
                    $banner->setPosition((int)$data->getPosition());
                }
                $banner->setPage($data->getPage());
                $banner->setMedia($media);
                $this->helper->em->persist($banner);
                // Flush in database
                $this->helper->em->flush();

                // Redirect and add flashbag
                $flashbagText = $editmode ? 'modifiée' : 'enregistrée';
                $this->helper->addFlashBag("La bannière a bien été $flashbagText");
                return $this->redirectToRoute('banners');
            }

            return $this->render('back/banner/form.html.twig', [
                'nav' => 'banner',
                'title' => 'Ajout d\'une bannière',
                'form' => $form->createView(),
                'media' => $media,
                'banner' => $banner,
                'editMode' => $editmode,
            ]);

        }
        return $this->redirectToRoute('login');
    }
}
