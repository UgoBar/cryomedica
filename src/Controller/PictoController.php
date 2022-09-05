<?php


namespace App\Controller;


use App\Entity\CryoMedia;
use App\Entity\CryoPicto;
use App\Form\CryoPictoType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PictoController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/pictos', name: 'pictos')]
    public function pictos(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $homePictos = $this->helper->em->getRepository(CryoPicto::class)->findBy(
                ['page' => 'home'],
                ['position' => 'ASC']
            ) ?? false;

            $elecPictos = $this->helper->em->getRepository(CryoPicto::class)->findBy(
                ['page' => 'elec'],
                ['position' => 'ASC']
            ) ?? false;

            if(isset($_POST['home-order'])) {
                foreach ($homePictos as $picto) {
                    $picto->setPosition($_POST['homePicto-'.$picto->getId().'-position']);
                    $this->helper->em->persist($picto);
                }
                $this->helper->em->flush();

                return $this->redirectToRoute('pictos');
            }

            if(isset($_POST['elec-order'])) {
                foreach ($elecPictos as $picto) {
                    $picto->setPosition($_POST['elecPicto-'.$picto->getId().'-position']);
                    $this->helper->em->persist($picto);
                }
                $this->helper->em->flush();

                return $this->redirectToRoute('pictos');
            }

            return $this->render('back/picto/list.html.twig', [
                'nav' => 'pictos',
                'title' => 'Liste des pictogrammes',
                'homePictos' => $homePictos,
                'elecPictos' => $elecPictos,
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/picto/ajout', name: 'create_picto')]
    #[Route('/admin/picto/edit/{id}', name: 'edit_picto')]
    public function form(Request $request, CryoPicto $picto = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$picto) {
                $media = new CryoMedia();
                $picto = new CryoPicto();
            } else {
                $editmode = true;
                $media = $picto->getMedia();
            }

            $form = $this->createForm(CryoPictoType::class, $picto);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record Picto with previously created Media
                if($data->getPage() === 'home') {
                    $picto->setTitle($data->getTitle());
                    $picto->setColor($data->getColor());
                    $picto->setBgColor($data->getBgColor());
                }
                $picto->setPage($data->getPage());
                $picto->setPosition($data->getPosition());
                $picto->setMedia($media);
                $this->helper->em->persist($picto);
                // Flush in database
                $this->helper->em->flush();

                // Redirect to the banner's list
                return $this->redirectToRoute('pictos');
            }

            return $this->render('back/picto/form.html.twig', [
                'nav' => 'pictos',
                'title' => 'Ajout d\'un pictogramme',
                'editMode' => $editmode ?? false,
                'media' => $media,
                'picto' => $picto,
                'form' => $form->createView(),
                'bgColor' => $picto->getBgColor() ?? 'rgb(213, 69, 175)',
                'color'   => $picto->getColor() ?? 'rgb(255, 255, 255)',
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/picto/delete', name: 'delete_picto')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $pictoId = (int)$request->request->all()['id'];
            $picto = $this->helper->em->getRepository(CryoPicto::class)->find($pictoId);
            $this->helper->em->remove($picto);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }
}
