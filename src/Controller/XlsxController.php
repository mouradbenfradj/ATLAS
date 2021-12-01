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
     * dbfService variable
     *
     * @var DbfService
     */
    private $dbfService;
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
     * @Route("/upload/{user}", name="xlsx_upload", methods={"GET","POST"})
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function upload(Request $request, User $user): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $xlsx = $form->get('upload')->getData();
            if ($xlsx) {
                $reader = new Xlsx();
                $spreadsheet = $reader->load($xlsx);
                $ignoredDay = array_merge($this->dbfService->dateDbfInDb($user), $this->pointageService->dateInDB($user), $this->jourFerierService->jourFerier());
                $manager = $this->getDoctrine()->getManager();
                $sheetCount = $spreadsheet->getSheetCount();
                for ($i = 0; $i < $sheetCount; $i++) {
                    $sheet = $spreadsheet->getSheet($i);
                    $sheetData = $sheet->toArray(null, true, true, true);
                    $this->xlsxService->construct($record->userid, $record->badgenumbe, $record->ssn, $record->username, $record->autosch, $record->attdate, $record->schid, $record->clockintim, $record->clockoutti, $record->starttime, $record->endtime, $record->workday, $record->realworkda, $record->late, $record->early, $record->absent, $record->overtime, $record->worktime, $record->exceptioni, $record->mustin, $record->mustout, $record->deptid, $record->sspedaynor, $record->sspedaywee, $record->sspedayhol, $record->atttime, $record->attchktime, $user);

                    foreach ($sheetData as  $ligne) {
                        if ($this->dateService->isDate($ligne['A']) and isset($horaires[$ligne['B']])) {
                            $dateString = $this->dateService->dateToStringY_m_d($ligne['A']);
                            $isJourFerier = $this->jourFerierService->isJourFerier($dateString);
                            $date = $this->dateService->dateString_d_m_Y_ToDateTime($ligne['A']);
                            if (
                                $isJourFerier
                                or
                                $ligne['C'] == 'CP'
                                or
                                $this->timeService->isTimeHi($ligne['C'])
                                or
                                $this->timeService->isTimeHi($ligne['D'])
                                or
                                in_array($ligne['K'], ['1', '1.5'])
                                or
                                $ligne['L']
                                or
                                $nowDate >= $date
                            ) {
                                $horaire = $horaires[$ligne['B']];
                                if (!$isJourFerier and !in_array($dateString, $arrayDate)) {
                                    array_push($arrayDate, $dateString);
                                    $user = $this->pointageService->addLigne($ligne, $user);
                                }
                            } else
                                $this->flash->add('danger ', 'ignored ligne ' . implode(" | ", $ligne));
                        }
                    }
                }
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
