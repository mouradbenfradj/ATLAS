<?php

namespace App\Controller;

use App\Entity\User;
use XBase\TableReader;
use App\Form\UploadType;
use App\Service\XlsxService;
use App\Service\CongerService;
use App\Service\AbsenceService;
use App\Service\EmployerService;
use App\Service\PointageService;
use App\Service\JourFerierService;
use App\Service\PointageGeneratorService;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Service\AutorisationSortieService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Service\PhpSpreadsheetService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/xlsx")
 */
class XlsxController extends AbstractController
{

    /**
     * adminUrlGenerator
     *
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;

    public function __construct(
        FlashBagInterface $flash,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->flash = $flash;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    /**
     * index
     * @Route("/", name="xlsx")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('xlsx/index.html.twig', [
            'controller_name' => 'XlsxController',
        ]);
    }


    /**
     * upload
     * @Route("/upload/{employer}", name="xlsx_upload", methods={"GET","POST"})
     *
     * @param Request $request
     * @param User $employer
     * @return Response
     */
    public function upload(Request $request, User $employer, PhpSpreadsheetService $phpSpreadsheetService): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $xlsx = $form->get('upload')->getData();
            if ($xlsx) {
                $phpSpreadsheetService->setEmployer($employer);
                $user= $phpSpreadsheetService->installXlsxFile($xlsx);
                dd($user);
                $manager = $this->getDoctrine()->getManager();
                /* for ($i = 0; $i < $sheetCount; $i++) {
                    $worksheet = $spreadsheet->getSheet($i);
                    $highestRow = $worksheet->getHighestRow();
                    dd($highestRow);
                    $cols = $worksheet->rangeToArray(
                        'A1:O'.$highestRow,
                        null,
                        true,
                        true,
                        true
                    );
                    foreach ($cols as $col) {
                        if ($this->dateService->isDate($col['A']) and $col['C'] and $col['D']) {
                            $this->xlsxService->construct($col, $employer);
                            $xlsx = $this->xlsxService->createEntity();
                            $employer->addXlsx($xlsx);
                        }
                        $employer->setSoldConger($this->employerService->calculerSoldConger($employer));
                        $employer->setSoldAutorisationSortie($this->employerService->calculerAS($employer));
                        $manager->persist($employer);
                        $manager->flush();
                    }
                } */

                $employer->setSoldConger($this->employerService->calculerSoldConger($employer));
                $employer->setSoldAutorisationSortie($this->employerService->calculerAS($employer));
                $manager->persist($employer);
                $manager->flush();
                $this->addFlash('success', 'Upload XLSX Successfully');
            }
            $url = $this->adminUrlGenerator
                ->setController(XlsxController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }
        return $this->render('xlsx/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
