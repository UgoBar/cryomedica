<?php

namespace App\Controller;

use App\Entity\CryoCenters;
use App\Repository\CryoCentersRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    private \Doctrine\Persistence\ObjectManager $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'page_name' => 'Accueil',
        ]);
    }

    /**
     * @Route("/centres", name="centers")
     * @throws \Exception
     */
    public function participate(): Response
    {
        /** @var CryoCentersRepository $CentersRepo */
        $centersRepo = $this->em->getRepository(CryoCenters::class);

        /** @var CryoCenters $centers */
        $centers = $centersRepo->findAll();
        $centersArray = [];

        $errors = [];

        foreach ($centers as $center) {
            $intermediateArray = [
                'id' => $center->getId(),
                'name' => $center->getName(),
                'address' => $center->getAddress(),
                'city' => $center->getCity(),
                'zip' => $center->getZip(),
                'lat' => $center->getLatitude(),
                'lng' => $center->getLongitude(),
                'isOpen' => $center->isIsOpen()
            ];
            array_push($centersArray, $intermediateArray);
        }

        if(isset($_POST['center'])) {

            $centerId     = (int)$_POST['center'] ?? 0;
            $center       = $centersRepo->find($centerId);
            $commitment   = isset($_POST['commitment']) ? trim($_POST['commitment']) : false;

            // Check if center exist ?
            if($center) {

                $isCenterOpen = $center->isIsOpen();

                // Check if radio input commitment matches with 'pre-order' or 'become-partner'
                if($commitment === 'become-partner' || $commitment === 'pre-order') {

                    // General data recevied
                    $firstname = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
                    $lastname  = (isset($_POST['lastname'])) ? trim($_POST['lastname']) : '';
                    $phone     = (isset($_POST['phone'])) ? trim($_POST['phone']) : '';
                    $email     = trim($_POST['email']);

                    // General validations
                    if (!$firstname || strlen($firstname) < 3 )
                        $errors['firstname'] = true;
                    if (!$lastname || strlen($lastname) < 3)
                        $errors['lastname'] = true;
                    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
                        $errors['email'] = true;
                    if(!$phone)
                        $errors['phone'] = true;

                    // Case 1 - BECOME PARTNER
                    if($commitment === 'become-partner') {
                        // Validation
                        $company = (isset($_POST['company'])) ? trim($_POST['company']) : '';

                        if(!$company)
                            $errors['company'] = true;
                    }

                    // Case 2 - PRE-ORDER
                    if($commitment === 'pre-order' && $isCenterOpen) {
                        // Validation
                        $sessionNumber = (isset($_POST['number'])) ? trim($_POST['number']) : '';

                        if(gettype($sessionNumber) !== 'integer' && $sessionNumber < 1)
                            $errors['sessionNumber'] = true;
                    }

                    if(!$errors) {
                        // Send mail
                        // Redirect
                        return $this->redirectToRoute('mailSent');
                    }

                } else {
//                    throw new \Exception('Il y a une erreur sur la partie 2 du formulaire');
                }
            } else {
//                throw new \Exception('Ce centre n\'existe pas');
            }
        }

        return $this->render('front/centers.html.twig', [
            'page_name' => 'Les centres',
            'centers_array' => $centersArray,
            'errors' => $errors,
            'routeName' => 'participate'
        ]);
    }

    /**
     * @Route("/mailSent", name="mailSent")
     */
    public function mailSent(): Response
    {
        return $this->render('front/mailSent.html.twig', [
            'page_name' => 'Demande envoyée !',
        ]);
    }

    /**
     * @Route("/clients", name="customers")
     */
    public function customers(): Response
    {
        return $this->render('front/customers.html.twig', [
            'page_name' => 'Clients',
        ]);
    }

    /**
     * @Route("/historique", name="historic")
     */
    public function historic(): Response
    {
        return $this->render('front/historic.html.twig', [
            'page_name' => 'Historique',
        ]);
    }

    /**
     * @Route("/cabines", name="cabins")
     */
    public function cabins(): Response
    {
        return $this->render('front/cabins.html.twig', [
            'page_name' => 'Cabines',
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about(): Response
    {
        return $this->render('front/about.html.twig', [
            'page_name' => 'A propos',
        ]);
    }
}
