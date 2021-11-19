<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use XBase\TableReader;
use App\Entity\Pointage;
use App\Form\UploadType;
use App\Service\DateService;
use App\Service\JourFerierService;
use App\Service\PointageGeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Repository\DbfRepository;
use App\Service\AbscenceService;
use App\Service\AutorisationSortieService;
use App\Service\CongerService;
use App\Service\DbfService;
use App\Service\EmployerService;
use App\Service\HoraireService;
use App\Service\PointageService;
use App\Service\TimeService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
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
    private $jourFerierService;
    private $pointageService;
    private $timeService;
    private $employerService;


    public function __construct(
        AdminUrlGenerator $adminUrlGenerator,
        DateService $dateService,
        JourFerierService $jourFerierService,
        PointageGeneratorService $pointageGeneratorService,
        HoraireService $horaireService,
        PointageService $pointageService,
        FlashBagInterface $flash,
        TimeService $timeService,
        CongerService $congerService,
        DbfService $dbfService,
        AbscenceService $abscenceService,
        AutorisationSortieService $autorisationSortieService,
        EmployerService $employerService
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->dateService = $dateService;
        $this->jourFerierService = $jourFerierService;
        $this->pointageGeneratorService = $pointageGeneratorService;
        $this->congerService = $congerService;
        $this->horaireService = $horaireService;
        $this->pointageService = $pointageService;
        $this->timeService = $timeService;
        $this->autorisationSortieService = $autorisationSortieService;
        $this->flash = $flash;
        $this->dbfService = $dbfService;
        $this->employerService = $employerService;
        $this->abscenceService = $abscenceService;
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
     * upload
     * @Route("/upload/{user}", name="dbf_upload", methods={"GET","POST"})
     *
     * @param Request $request
     * @param DbfService $dbfService
     * @param User $user
     * @return Response
     */
    public function upload(Request $request, DbfRepository $dbfRepository, DbfService $dbfService, User $user): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $dateDbfInDb = $dbfService->dateDbfInDb($user);
                $datePointageInDB = $this->pointageService->dateInDB($user);
                $inDb = array_merge($dateDbfInDb, $datePointageInDB);
                $jourFerier = $this->jourFerierService->jourFerier();
                $ignoredDay = array_merge($inDb, $jourFerier);
                $manager = $this->getDoctrine()->getManager();
                $dbfs = new TableReader($dbf);
                while ($record = $dbfs->nextRecord()) {
                    $dateDbf = $this->dateService->dateString_d_m_Y_ToDateTime($record->attdate);
                    if (!in_array($dateDbf->format('Y-m-d'), $ignoredDay)) {
                        $dbfService->construct($record->userid, $record->badgenumbe, $record->ssn, $record->username, $record->autosch, $record->attdate, $record->schid, $record->clockintim, $record->clockoutti, $record->starttime, $record->endtime, $record->workday, $record->realworkda, $record->late, $record->early, $record->absent, $record->overtime, $record->worktime, $record->exceptioni, $record->mustin, $record->mustout, $record->deptid, $record->sspedaynor, $record->sspedaywee, $record->sspedayhol, $record->atttime, $record->attchktime, $user);
                        $dbf = $dbfService->createEntity();
                        if (!$this->dateService->isWeek($dateDbf) or ($dbf->getStarttime() or $dbf->getEndtime())) {
                            $user->addDbf($dbf);
                            $manager->persist($user);
                        }
                    }
                }
                $manager->flush();
                foreach ($user->getDbfs() as $dbf) {
                    $this->pointageService->constructFromDbf($dbf);
                    $pointage = $this->pointageService->createEntity();
                    $accespted = ($pointage->getEntrer() and $pointage->getSortie())
                    or ($pointage->getCongerPayer() and $pointage->getCongerPayer()->getValider())
                    or $pointage->getAbscence();

                    if ($accespted) {
                        $user->addPointage($pointage);
                        $manager->remove($dbf);
                    }
                }
                $user->setSoldConger($this->employerService->calculerSoldConger($user));
                $user->setSoldAutorisationSortie($this->employerService->calculerAS($user));

                $manager->persist($user);
                $manager->flush();


                //dd($user->getPointages()->toArray());





                /* $conger = $this->congerService->getIfConger($dateDbf, $user);
                    $user = $this->congerService->getemployer();
                    $autorisationSortie = $this->autorisationSortieService->getIfAutorisationSortie($dateDbf, $user);
                    $pointage = new Pointage();
                    $pointage->setDate($this->dateService->dateString_d_m_Y_ToDateTime($record->attdate));
                    $pointage->setHoraire($this->horaireService->getHoraireForDate($pointage->getDate()));
                    $pointage->setCongerPayer($conger);
                    $pointage->setAutorisationSortie($autorisationSortie);
                    $pointage->setAbscence(null);


                    if ($record->starttime != "" and $this->timeService->isTimeHi($record->starttime))
                        $pointage->setEntrer(new DateTime($record->starttime));
                    else {
                        $pointage->setEntrer(new DateTime("00:00:00"));
                        $this->flash->add('danger ', 'saisie automatique de l\'heur d\'entrer a 00:00:00 pour la date ' . $record->attdate);
                    }
                    if ($record->endtime != ""  and $this->timeService->isTimeHi($record->endtime))
                        $pointage->setSortie(new DateTime($record->endtime));
                    else {
                        $pointage->setSortie(new DateTime("23:00:00"));
                        $this->flash->add('danger ', 'saisie automatique de l\'heur de sortie a 23:00:00 pour la date ' . $record->attdate);
                    }


                    $this->pointageService->setPointage($pointage);
                    $pointage->setNbrHeurTravailler($this->pointageService->nbrHeurTravailler());
                    $pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
                    $pointage->setDepartAnticiper(null);
                    $pointage->setRetardMidi(null);
                    $pointage->setTotaleRetard($this->pointageService->totalRetard());
                    $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
                    $pointage->setDiff($this->pointageService->diff());
                    $user->addPointage($pointage); */

                $manager->flush();
                $this->addFlash('success', 'id.updated_successfully');
            }
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
