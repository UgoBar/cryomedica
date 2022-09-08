<?php


namespace App\Controller;


use App\Entity\CryoAbout;
use App\Form\CryoAboutType;
use App\Helper\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    #[Route('/admin/about', name: 'back_about')]
    public function form(Request $request): Response
    {

        if($this->helper->verifyConnection()) {

            $flashbag = $this->helper->getFlashBag() ?? false;

            $about = $this->helper->em->getRepository(CryoAbout::class)
                ->findAll()[0] ?? false;

            if (!$about) {
                $about = new CryoAbout();
                $editmode = false;
            } else {
                $editmode = true;
            }

            $form = $this->createForm(CryoAboutType::class, $about);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                $about->setFirstText($data->getFirstText());
                $about->setSecondText($data->getSecondText());

                $this->helper->em->persist($about);
                $this->helper->em->flush();

                // Redirect and add flashbag
                $this->helper->addFlashBag('La modification a été enregistrée avec succès');
                return $this->redirectToRoute('back_about');
            }

            return $this->render('back/about/form.html.twig', [
                'nav' => 'about',
                'editMode' => $editmode,
                'title' => 'Contenu de la page "A propos"',
                'about' => $about,
                'flashbag' => $flashbag,
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('login');
    }
}
