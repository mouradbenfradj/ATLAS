<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadType;
use App\Service\PointageGeneratorService;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/xlsx")
 */
class XlsxController extends AbstractController
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
     * @Route("/", name="xlsx")
     */
    public function index(): Response
    {
        return $this->render('xlsx/index.html.twig', [
            'controller_name' => 'XlsxController',
        ]);
    }


    /**
     * @Route("/upload/{user}", name="xlsx_upload", methods={"GET","POST"})
     */
    public function upload(Request $request,  PointageGeneratorService $pointageGeneratorService, User $user): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $xlsx = $form->get('upload')->getData();
            if ($xlsx) {
                $reader = new Xlsx();
                $spreadsheet = $reader->load($xlsx);
                $this->getDoctrine()->getManager()->persist($pointageGeneratorService->fromXlsxFile($spreadsheet, $user));
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'updated_successfully');
            }

            $url = $this->adminUrlGenerator
                ->setController(PointageCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
            //$table = new TableReader($dbf);
            //$pointageGenerator->fromDbfFile($table, $user);
        }
        return $this->render('xlsx/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
