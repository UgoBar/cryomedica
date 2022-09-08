<?php


namespace App\Controller;


use App\Entity\CryoElec;
use App\Entity\CryoElecArray;
use App\Form\CryoElecType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ElecController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/elec', name: 'elec')]
    public function form(Request $request): Response
    {

        if($this->helper->verifyConnection()) {

            $flashbag = $this->helper->getFlashBag() ?? false;

            $elec = $this->helper->em->getRepository(CryoElec::class)->findAll()[0] ?? false;

            if (!$elec) {
                $elec = new CryoElec();
                $editmode = false;
            } else {
                $editmode = true;
                $elecArray = $this->helper->em->getRepository(CryoElecArray::class)->findBy(
                    ['cryoElec' => $elec]
                );
            }

            $form = $this->createForm(CryoElecType::class, $elec);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // Then record Picto with previously created Media
                $elec->setText($data->getText());
                $this->helper->em->persist($elec);
                // Flush in database
                $this->helper->em->flush();
                // Redirect and add flashbag
                $flashbagText = $editmode ? 'modifié' : 'enregistré';
                $this->helper->addFlashBag("le texte d'introduction a bien été $flashbagText");
                return $this->redirectToRoute('elec');
            }

            return $this->render('back/elec/elec.html.twig', [
                'nav' => 'elec',
                'editMode' => $editmode,
                'title' => $editmode ? 'Modification de la page "cabines électriques"' : 'Ajout de la page "cabines électriques"',
                'elec' => $elec,
                'elecArray' => $elecArray ?? false,
                'form' => $form->createView(),
                'flashbag' => $flashbag,
            ]);
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/carac/add', name: 'add_carac')]
    public function addCarac(Request $request): JsonResponse
    {
        if($request->request->all()['elecId'] && $this->helper->verifyConnection()) {

            $elecId = (int)$request->request->all()['elecId'];
            $title = $request->request->all()['title'];
            $description = $request->request->all()['description'];

            if($elecId && $title && $description) {

                $elec = $this->helper->em->getRepository(CryoElec::class)->find($elecId);
                $elecArray = new CryoElecArray();

                $elecArray->setCryoElec($elec);
                $elecArray->setTitle($title);
                $elecArray->setDescription($description);

                $this->helper->em->persist($elecArray);
                $this->helper->em->flush();

                return new JsonResponse(['actionResponse'=>'success', 'caracId' => $elecArray->getId()]);
            }
            return new JsonResponse(['actionResponse'=> 'Data corrupted']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }

    #[Route('/admin/carac/edit', name: 'edit_carac')]
    public function editCarac(Request $request): JsonResponse
    {
        if($request->request->all()['caracId'] && $this->helper->verifyConnection()) {

            $caracId = (int)$request->request->all()['caracId'];
            $title = $request->request->all()['title'];
            $description = $request->request->all()['description'];

            if($caracId && $title && $description) {

                $elecArray = $this->helper->em->getRepository(CryoElecArray::class)->find($caracId);

                if($elecArray) {
                    $elecArray->setTitle($title);
                    $elecArray->setDescription($description);

                    $this->helper->em->persist($elecArray);
                    $this->helper->em->flush();
                    return new JsonResponse(['actionResponse'=>'success']);

                }
                return new JsonResponse(['actionResponse'=> 'Data corrupted']);
            }
            return new JsonResponse(['actionResponse'=> 'Data corrupted']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }

    #[Route('/admin/carac/delete', name: 'delete_carac')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['caracId'] && $this->helper->verifyConnection()) {
            $caracId = (int)$request->request->all()['caracId'];
            $caracRow = $this->helper->em->getRepository(CryoElecArray::class)->find($caracId);
            $this->helper->em->remove($caracRow);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);
    }
}
