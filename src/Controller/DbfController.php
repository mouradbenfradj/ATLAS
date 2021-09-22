<?php

namespace App\Controller;

use XBase\TableReader;
use App\Form\UploadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Entity\User;
use App\Service\PointageGeneratorService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{

    /**
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;

    /**
     * @param AdminUrlGenerator $adminUrlGenerator
     */
    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    /**
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
     * @Route("/upload/{id}", name="dbf_upload", methods={"GET","POST"})
     * @param Request $request
     * @param PointageGeneratorService $pointageGeneratorService
     * @param User $id
     * 
     * @return Response
     */
    public function upload(Request $request, PointageGeneratorService $pointageGeneratorService, User $id): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $dbfs = new TableReader($dbf);
                $pointageGeneratorService->fromDbfFile($dbfs, $id);
            }
            $url = $this->adminUrlGenerator
                ->setController(PointageCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }
        dump($id);
        return $this->render('dbf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
