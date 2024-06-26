<?php

namespace App\Controller;

use App\Entity\CryoAbout;
use App\Entity\CryoAzote;
use App\Entity\CryoBanner;
use App\Entity\CryoBio;
use App\Entity\CryoCenters;
use App\Entity\CryoContact;
use App\Entity\CryoCustomer;
use App\Entity\CryoElec;
use App\Entity\CryoElecArray;
use App\Entity\CryoHistoric;
use App\Entity\CryoPicto;
use App\Entity\CryoTestimony;
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

    #[Route('/templates/menu_photo', name: 'menu_photo')]
    public function menuPhoto(): Response
    {
        $banner1 = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'home', 'position' => 1])[0];
        $banner2 = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'home', 'position' => 2])[0];
        $banner3 = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'home', 'position' => 3])[0];

        return $this->render('front/menu_photo.html.twig', [
            'banner1' => $banner1,
            'banner2' => $banner2,
            'banner3' => $banner3,
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $banner1 = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'home', 'position' => 1])[0];
        $banner2 = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'home', 'position' => 2])[0];
        $banner3 = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'home', 'position' => 3])[0];

        $homePictos = $this->em->getRepository(CryoPicto::class)->findBy(
            ['page' => 'home'],
            ['position' => 'ASC']
        ) ?? false;

        $bio = $this->em->getRepository(CryoBio::class)->findAll()[0] ?? false;

        return $this->render('front/index.html.twig', [
            'title' => 'Accueil',
            'banner1' => $banner1,
            'banner2' => $banner2,
            'banner3' => $banner3,
            'pictos' => $homePictos,
            'bio' => $bio
        ]);
    }


    /**
     * @throws \Exception
     */
    #[Route('/centres', name: 'centers')]
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

            // Check if center exist ?
            if($center) {

                $centerName = $center->getName();
                $centerCity = $center->getCity();


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

                $message = "Bonjour Mr Favre, je vous contacte pour devenir Ambassadeur du centre $centerName situé à $centerCity";


                if(!$errors) {

                    // Set record in database
                    $contact = new CryoContact();

                    $contact->setCenter($center);
                    $contact->setFirstname($firstname);
                    $contact->setLastname($lastname);
                    $contact->setPhone($phone);
                    $contact->setEmail($email);
                    $contact->setCreatedAt(new \DateTimeImmutable());

                    $this->em->persist($contact);
                    $this->em->flush();

                    // Send mail
                    //$mailTo  = 'ugo17190@gmail.com';
                    $mailTo  = 'pfavre92@icloud.com';

                    $subject = $firstname . ' ' . $lastname . ' (' . $phone . ')';

                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'From: Cryomedica <contact@cryomedica.fr>'."\r\n";
                    $headers .= 'Reply-To: '.$email."\r\n";
                    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                    $headers .= 'X-Mailer: PHP/' . phpversion();

                    mail($mailTo, $subject, $message, $headers);
                    // Redirect
                    return $this->redirectToRoute('mailSent');
                }

            } else {
                throw new \Exception('Ce centre n\'existe pas');
            }
        }

        return $this->render('front/centers.html.twig', [
            'title' => 'Les centres',
            'centers_array' => $centersArray,
            'errors' => $errors,
            'routeName' => 'centers'
        ]);
    }

    #[Route('/contact', name: 'front_contact')]
    public function contact(): Response
    {
        $errors = [];

        if(isset($_POST['lastname'])) {
            // General data recevied
            $firstname = strip_tags(trim($_POST['firstname']));
            $lastname = strip_tags(trim($_POST['lastname']));
            $phone = strip_tags(trim($_POST['phone']));
            $email = strip_tags(trim($_POST['email']));
            $message = strip_tags(trim($_POST['message']));

            // General validations
            if (!$firstname || strlen($firstname) < 3 )
                $errors['firstname'] = true;
            if (!$lastname || strlen($lastname) < 3)
                $errors['lastname'] = true;
            if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
                $errors['email'] = true;
            if(!$phone)
                $errors['phone'] = true;
            if(!$message)
                $errors['content'] = true;

            if(!$errors) {

                // Send mail
                $mailTo  = 'pfavre92@icloud.com';

                $subject = $firstname . ' ' . $lastname . ' (' . $phone . ')';

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Cryomedica <contact@cryomedica.fr>'."\r\n";
                $headers .= 'Reply-To: '.$email."\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();

                mail($mailTo, $subject, $message, $headers);
                // Redirect
                return $this->redirectToRoute('mailSent');
            }
        }

        return $this->render('front/contact.html.twig', [
            'title' => 'Contact',
        ]);
    }

    #[Route('/mailSent', name: 'mailSent')]
    public function mailSent(): Response
    {
        return $this->render('front/mailSent.html.twig', [
            'title' => 'Demande envoyée !',
        ]);
    }

    #[Route('/clients', name: 'customers')]
    public function customers(): Response
    {
        $customers = $this->em->getRepository(CryoCustomer::class)->findBy(
            [],
            ['position' => 'ASC']
        ) ?? false;

        $testimonials = $this->em->getRepository(CryoTestimony::class)->findAll() ?? false;

        return $this->render('front/customers.html.twig', [
            'title' => 'Clients',
            'customers' => $customers,
            'testimonials' => $testimonials
        ]);
    }

    #[Route('/historique', name: 'historic')]
    public function historic(): Response
    {
        $historics = $this->em->getRepository(CryoHistoric::class)->findBy(
            [],
            ['date' => 'ASC']
        ) ?? false;

        return $this->render('front/historic.html.twig', [
            'title' => 'Historique',
            'historics' => $historics
        ]);
    }

    #[Route('/cabines', name: 'cabins')]
    public function cabins(): Response
    {
        // BANNERS
        $elecBanner = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'elec'], ['id' => 'DESC'])[0];
        $azoteBanner = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'azote'], ['id' => 'DESC'])[0];

        // TEXT
        $elec = $this->em->getRepository(CryoElec::class)->findAll()[0] ?? false;

        // ARRAY
        if($elec) {
            $elecArray = $this->em->getRepository(CryoElecArray::class)->findBy(['cryoElec' => $elec]);
        }

        // PICTOS
        $elecPictos = $this->em->getRepository(CryoPicto::class)->findBy(['page' => 'elec'], ['position' => 'ASC']) ?? false;

        // AZOTE CABINS
        $azote = $this->em->getRepository(CryoAzote::class)->findBy([], ['position' => 'ASC']) ?? false;

        return $this->render('front/cabins.html.twig', [
            'title' => 'Cabines',
            'elecBanner' => $elecBanner,
            'azoteBanner' => $azoteBanner,
            'elec' => $elec,
            'elecArray' => $elecArray ?? false,
            'pictos' => $elecPictos,
            'azoteCabins' => $azote
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        $about = $this->em->getRepository(CryoAbout::class)->findAll()[0] ?? false;
        $aboutBanner = $this->em->getRepository(CryoBanner::class)->findBy(['page' => 'about'], ['id' => 'DESC'])[0] ?? false;

        return $this->render('front/about.html.twig', [
            'title' => 'A propos',
            'about' => $about,
            'aboutBanner' => $aboutBanner
        ]);
    }
}
