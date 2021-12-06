<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Service\TableReaderService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{

  
    public function __construct(FlashBagInterface $flash, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->flash = $flash;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * index
     * @Route("/", name="dbf")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('dbf/index.html.twig', [
            'controller_name' => 'DbfController',
        ]);
    }

    /**
     * upload function
     * @Route("/upload/{employer}", name="dbf_upload", methods={"GET","POST"})
     *
     * @param Request $request
     * @param User $employer
     * @return Response
     */
    public function upload(Request $request, User $employer): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $this->tableReaderService->setEmployer($employer);
                $this->tableReaderService->installDbfFile($dbf);

                $manager = $this->getDoctrine()->getManager();
            }
            $user->setSoldConger($this->employerService->calculerSoldConger($user));
            $user->setSoldAutorisationSortie($this->employerService->calculerAS($user));
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'id.updated_successfully');

            $url = $this->adminUrlGenerator
                ->setController(PointageCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('dbf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
