<?php


namespace App\Controller;

use App\Helper\Helper;
use App\Entity\CryoCustomer;
use App\Entity\CryoMedia;
use App\Form\CryoCustomerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Customer extends AbstractController
{

    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/customers', name: 'back_customers')]
    public function pictos(Request $request): Response
    {
        if($this->helper->verifyConnection()) {

            $customers = $this->helper->em->getRepository(CryoCustomer::class)->findBy(
                    [],
                    ['position' => 'ASC']
                ) ?? false;

            if(isset($_POST['order'])) {
                foreach ($customers as $customer) {
                    $customer->setPosition($_POST['customer-'.$customer->getId().'-position']);
                    $this->helper->em->persist($customer);
                }
                $this->helper->em->flush();

                return $this->redirectToRoute('back_customers');
            }

            return $this->render('back/customers/list.html.twig', [
                'nav' => 'customers',
                'title' => 'Liste des clients',
                'customers' => $customers,
            ]);
        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/customer/ajout', name: 'create_customer')]
    #[Route('/admin/customer/edit/{id}', name: 'edit_customer')]
    public function form(Request $request, CryoCustomer $customer = null): Response
    {
        if($this->helper->verifyConnection()) {

            if(!$customer) {
                $media = new CryoMedia();
                $customer = new CryoCustomer();
            } else {
                $editmode = true;
                $media = $customer->getMedia();
            }

            $form = $this->createForm(CryoCustomerType::class, $customer);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                // First record Media
                $media->setImageFile($data->getMedia()->getImageFile());
                $media->setAlt($data->getMedia()->getAlt());
                $this->helper->em->persist($media);

                // Then record Picto with previously created Media
                $customer->setName($data->getName(

                ));
                $customer->setPosition($data->getPosition());
                $customer->setMedia($media);
                $this->helper->em->persist($customer);
                // Flush in database
                $this->helper->em->flush();

                // Redirect to the banner's list
                return $this->redirectToRoute('back_customers');
            }

            return $this->render('back/customers/form.html.twig', [
                'nav' => 'customers',
                'title' => 'Ajout d\'un client',
                'editMode' => $editmode ?? false,
                'media' => $media,
                'customer' => $customer,
                'form' => $form->createView(),
            ]);

        }
        return $this->redirectToRoute('login');
    }

    #[Route('/admin/customers/delete', name: 'delete_customer')]
    public function delete(Request $request): JsonResponse
    {
        if($request->request->all()['id'] && $this->helper->verifyConnection()) {
            $customerId = (int)$request->request->all()['id'];
            $customer = $this->helper->em->getRepository(CryoCustomer::class)->find($customerId);
            $this->helper->em->remove($customer);
            $this->helper->em->flush();
            return new JsonResponse(['actionResponse'=>'success']);
        }
        return new JsonResponse(['actionResponse'=>'unauthorized']);

    }
}
