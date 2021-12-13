<?php

namespace App\Controller;

use App\Controller\Admin\DbfCrudController;
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
use XBase\TableReader;

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
    public function upload(Request $request, User $employer, TableReaderService $tableReaderService): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $tableReaderService->setEmployer($employer);
                $employer = $tableReaderService->installDbfFile($dbf);
                /* dd($user);
                $user->setSoldConger($this->employerService->calculerSoldConger($user));
                $user->setSoldAutorisationSortie($this->employerService->calculerAS($user)); */
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($employer);
                $manager->flush();
                $this->addFlash('success', 'upload DBF Successfully');
            }
            $url = $this->adminUrlGenerator
                ->setController(DbfCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('dbf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
