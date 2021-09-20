<?php

namespace App\Controller;

use XBase\TableReader;
use App\Form\UploadType;
use App\Service\PointageGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{

    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    /**
     * @Route("/", name="dbf")
     */
    public function index(): Response
    {
        return $this->render('dbf/index.html.twig', [
            'controller_name' => 'DbfController',
        ]);
    }


    /**
     * @Route("/upload/{id}", name="dbf_upload", methods={"GET","POST"})
     */
    public function upload(Request $request, PointageGenerator $pointageGenerator, $id): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $table = new TableReader($dbf);
                $pointageGenerator->fromDbfFile($table, $id);
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
