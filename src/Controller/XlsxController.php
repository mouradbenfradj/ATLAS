<?php

namespace App\Controller;

use App\Entity\User;
use XBase\TableReader;
use App\Form\UploadType;
use App\Service\DateService;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/{_locale}/xlsx")
 */
class XlsxController extends AbstractController
{

    /**
     * adminUrlGenerator
     *
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;
    /**
     * dateService
     *
     * @var DateService
     */
    private $dateService;
    /**
     * jourFerierService variable
     *
     * @var JourFerierService
     */
    private $jourFerierService;
    /**
     * pointageService variable
     *
     * @var PointageService
     */
    private $pointageService;
    /**
     * employerService variable
     *
     * @var EmployerService
     */
    private $employerService;
    /**
     * xlsxService variable
     *
     * @var XlsxService
     */
    private $xlsxService;
    /**
     * absenceService variable
     *
     * @var AbsenceService
     */
    private $absenceService;
    /**
     * congerService variable
     *
     * @var CongerService
     */
    private $congerService;
    /**
     * autorisationSortieService variable
     *
     * @var AutorisationSortieService
     */
    private $autorisationSortieService;


    public function __construct(
        FlashBagInterface $flash,
        AdminUrlGenerator $adminUrlGenerator,
        DateService $dateService,
        JourFerierService $jourFerierService,
        PointageService $pointageService,
        EmployerService $employerService,
        //HoraireService $horaireService,
        XlsxService $xlsxService,
        AbsenceService $absenceService,
        CongerService $congerService,
        AutorisationSortieService $autorisationSortieService
    ) {
        $this->flash = $flash;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->dateService = $dateService;
        $this->jourFerierService = $jourFerierService;
        //$this->horaireService = $horaireService;
        $this->pointageService = $pointageService;
        $this->employerService = $employerService;
        $this->xlsxService = $xlsxService;
        $this->absenceService = $absenceService;
        $this->congerService = $congerService;
        $this->autorisationSortieService = $autorisationSortieService;
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
    public function upload(Request $request, User $employer): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $xlsx = $form->get('upload')->getData();
            if ($xlsx) {
                $reader = new Xlsx();
                $spreadsheet = $reader->load($xlsx);
                $ignoredDay = array_merge($this->xlsxService->dateInDb($employer), $this->pointageService->dateInDB($employer), $this->jourFerierService->jourFerier());
                $manager = $this->getDoctrine()->getManager();
                $allSheet = $spreadsheet->getAllSheets();
                foreach ($allSheet as $worksheet) {
                    $highestRow = $worksheet->getHighestRow();
                    $rows = $worksheet->rangeToArray(
                        'A1:O' . $highestRow,
                        null,
                        true,
                        true,
                        true
                    );
                    foreach ($rows as $cols) {
                        if ($this->dateService->isDate($cols['A']) and $cols['C'] and $cols['D']) {
                            $dateXlsx =  $this->dateService->dateString_d_m_Y_ToDate_Y_m_d($cols['A']);
                            if (!in_array($dateXlsx->format('Y-m-d'), $ignoredDay)) {
                                $this->xlsxService->construct($cols, $employer);
                                $xlsx = $this->xlsxService->createEntity();
                                $employer->addXlsx($xlsx);
                            }
                        }
                    }
                }/* for ($i = 0; $i < $sheetCount; $i++) {
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
                $this->addFlash('success', 'updated_successfully');
            }
            $url = $this->adminUrlGenerator
                ->setController(PointageCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }
        return $this->render('xlsx/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
