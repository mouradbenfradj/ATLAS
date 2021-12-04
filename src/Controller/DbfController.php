<?php

namespace App\Controller;

use App\Entity\User;
use XBase\TableReader;
use App\Form\UploadType;
use App\Service\DateService;
use App\Service\JourFerierService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Service\AbsenceService;
use App\Service\AutorisationSortieService;
use App\Service\CongerService;
use App\Service\DbfService;
use App\Service\EmployerService;
use App\Service\HoraireService;
use App\Service\PointageService;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/{_locale}/dbf")
 */
class DbfController extends AbstractController
{
    /**
     * userid variable
     *
     * @var float
     */
    private $userid;

    /**
     * badgenumbe variable
     *
     * @var int
     */
    private $badgenumbe;

    /**
     * ssn variable
     *
     * @var string
     */
    private $ssn;

    /**
     * username variable
     *
     * @var string
     */
    private $username;

    /**
     * autosch variable
     *
     * @var string|null
     */
    private $autosch;

    /**
     * attdate variable
     *
     * @var DateTime
     */
    private $attdate;

    /**
     * schid variable
     *
     * @var float|null
     */
    private $schid;

    /**
     * clockintim variable
     *
     * @var DateTime|null
     */
    private $clockintim;

    /**
     * clockoutti variable
     *
     * @var DateTime|null
     */
    private $clockoutti;

    /**
     * starttime variable
     *
     * @var DateTime|null
     */
    private $starttime;

    /**
     * endtime variable
     *
     * @var DateTime|null
     */
    private $endtime;

    /**
     * workday variable
     *
     * @var float|null
     */
    private $workday;

    /**
     * realworkda variable
     *
     * @var float|null
     */
    private $realworkda;

    /**
     * late variable
     *
     * @var DateTime|null
     */
    private $late;

    /**
     * early variable
     *
     * @var DateTime|null
     */
    private $early;

    /**
     * absent variable
     *
     * @var float|null
     */
    private $absent;

    /**
     * overtime variable
     *
     * @var DateTime|null
     */
    private $overtime;

    /**
     * worktime variable
     *
     * @var DateTime|null
     */
    private $worktime;

    /**
     * exceptioni variable
     *
     * @var string|null
     */
    private $exceptioni;

    /**
     * mustin variable
     *
     * @var string|null
     */
    private $mustin;

    /**
     * mustout variable
     *
     * @var string|null
     */
    private $mustout;

    /**
     * deptid variable
     *
     * @var float|null
     */
    private $deptid;

    /**
     * sspedaynor variable
     *
     * @var float|null
     */
    private $sspedaynor;

    /**
     * sspedaywee variable
     *
     * @var float|null
     */
    private $sspedaywee;

    /**
     * sspedayhol variable
     *
     * @var float|null
     */
    private $sspedayhol;

    /**
     * atttime variable
     *
     * @var DateTime|null
     */
    private $atttime;

    /**
     * attchktime variable
     *
     * @var array|null
     */
    private $attchktime = [];

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
        DbfService $dbfService,
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
        $this->dbfService = $dbfService;
        $this->absenceService = $absenceService;
        $this->congerService = $congerService;
        $this->autorisationSortieService = $autorisationSortieService;
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
     * @Route("/upload/{user}", name="dbf_upload", methods={"GET","POST"})
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
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $ignoredDay = array_merge($this->dbfService->dateInDb($user), $this->pointageService->dateInDB($user), $this->jourFerierService->jourFerier());
                $manager = $this->getDoctrine()->getManager();
                $dbfs = new TableReader($dbf);
                while ($record = $dbfs->nextRecord()) {
                    $dateDbf = $this->dateService->dateString_d_m_Y_ToDateTime($record->attdate);
                    if (!in_array($dateDbf->format('Y-m-d'), $ignoredDay)) {
                        $this->dbfService->construct($record->userid, $record->badgenumbe, $record->ssn, $record->username, $record->autosch, $record->attdate, $record->schid, $record->clockintim, $record->clockoutti, $record->starttime, $record->endtime, $record->workday, $record->realworkda, $record->late, $record->early, $record->absent, $record->overtime, $record->worktime, $record->exceptioni, $record->mustin, $record->mustout, $record->deptid, $record->sspedaynor, $record->sspedaywee, $record->sspedayhol, $record->atttime, $record->attchktime, $user);
                        $dbf = $this->dbfService->createEntity();
                        $this->absenceService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                        $this->congerService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                        $this->autorisationSortieService->partielConstruct($dbf->getEmployer(), $dbf->getAttdate());
                        if (
                            !$this->dateService->isWeek($dateDbf)
                            and (
                                ($dbf->getStarttime() and $dbf->getEndtime())
                                or $this->absenceService->estAbscent()
                                or $this->congerService->estUnConger()
                                or $this->autorisationSortieService->getAutorisation())
                        ) {
                            $this->pointageService->constructFromDbf($dbf);
                            $pointage = $this->pointageService->createEntity();
                            /*  $accespted = ($pointage->getEntrer() and $pointage->getSortie())
                                 or ($pointage->getCongerPayer() and $pointage->getCongerPayer()->getValider())
                                 or $pointage->getAbsence();

                             if ($accespted) { */
                            $user->addPointage($pointage);
                        } else {
                            $user->addDbf($dbf);
                        }
                    }
                }
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
