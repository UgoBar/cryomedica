<?php


namespace App\Controller;


use App\Entity\CryoCenters;
use App\Form\CryoCentersType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CentersController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/centers', name: 'back_centers')]
    public function pictos(Request $request): Response
    {
        if ($this->helper->verifyConnection()) {

            $centers = $this->helper->em->getRepository(CryoCenters::class)->findBy(
                [],
                ['is_open' => 'DESC']
            ) ?? false;

            return $this->render('back/centers/list.html.twig', [
                'nav' => 'centers',
                'title' => 'Liste des centres',
                'centers' => $centers,
            ]);
        }

        return $this->redirectToRoute('login');
    }

    #[Route('/admin/center/ajout', name: 'create_center')]
    #[Route('/admin/center/edit/{id}', name: 'edit_center')]
    public function form(Request $request, CryoCenters $center = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$center) {
                $center = new CryoCenters();
                $editmode = false;
            } else {
                $editmode = true;
            }

            $form = $this->createForm(CryoCentersType::class, $center);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                $center->setName($data->getName());
                $center->setAddress($data->getAddress());
                $center->setCity($data->getCity());
                $center->setZip($data->getZip());
                $center->setLatitude($data->getLatitude());
                $center->setLongitude($data->getLongitude());
                $center->setIsOpen($data->isIsOpen());

                $this->helper->em->persist($center);
                $this->helper->em->flush();

                // Redirect to the banner's list
                return $this->redirectToRoute('back_centers');
            }

            return $this->render('back/centers/form.html.twig', [
                'nav' => 'pictos',
                'title' => 'Ajout d\'un pictogramme',
                'editMode' => $editmode,
                'center' => $center,
                'form' => $form->createView(),
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/center/delete', name: 'delete_center')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $centerId = (int)$request->request->all()['id'];
            $centerRepo = $this->helper->em->getRepository(CryoCenters::class);
            $center = $centerRepo->find($centerId);
            $this->helper->em->remove($center);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }
}
