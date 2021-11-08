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
use App\Entity\Abscence;
use App\Entity\AutorisationSortie;
use App\Entity\Conger;
use App\Entity\Dbf;
use App\Repository\AutorisationSortieRepository;
use App\Repository\DbfRepository;
use App\Service\AutorisationSortieService;
use App\Service\CongerService;
use App\Service\DbfService;
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
    private $pointageGeneratorService;
    private $horaireService;
    private $pointageService;


    /**
     * __construct
     *
     * @param AdminUrlGenerator $adminUrlGenerator
     * @param DateService $dateService
     * @param JourFerierService $jourFerierService
     * @param PointageGeneratorService $pointageGeneratorService
     * @param HoraireService $horaireService
     * @param PointageService $pointageService
     * @param FlashBagInterface $flash
     * @param TimeService $timeService
     * @param CongerService $congerService
     * @param AutorisationSortieService $autorisationSortieService
     * @param DbfService $dbfService
     */
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
        AutorisationSortieService $autorisationSortieService,
        DbfService $dbfService
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
     * @param User $user
     * @return Response
     */
    public function upload(Request $request, DbfService $dbfService, User $user): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $entityManager = $this->getDoctrine()->getManager();
                $dateDbfInDb = $dbfService->dateDbfInDb($user);
                $dbfs = new TableReader($dbf);
                $datePointageInDB = $this->pointageGeneratorService->dateInDB($user);
                $inDb = array_merge($dateDbfInDb, $datePointageInDB);
                //$inDB = $this->pointageGeneratorService->dateInDB($user);
                while ($record = $dbfs->nextRecord()) {
                    //$dateDbf = $this->dateService->dateToStringY_m_d($record->attdate);
                    $dateDbf = $this->dateService->dateString_d_m_Y_ToDateTime($record->attdate);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateDbf->format("Y-m-d"));
                    if (!$isJourFerier and !in_array($dateDbf->format('Y-m-d'), $inDb)) {

                        $dbf = $dbfService->construct($record->userid, $record->badgenumbe, $record->ssn, $record->username, $record->autosch, $record->attdate, $record->schid, $record->clockintim, $record->clockoutti, $record->starttime, $record->endtime, $record->workday, $record->realworkda, $record->late, $record->early, $record->absent, $record->overtime, $record->worktime, $record->exceptioni, $record->mustin, $record->mustout, $record->deptid, $record->sspedaynor, $record->sspedaywee, $record->sspedayhol, $record->atttime, $record->attchktime, $user);
                        $dbf = $dbfService->createEntity($user);


                        //if (!($dateDbf->format("w") == 0) and !($dateDbf->format("w") == 6) and !$dbf->getStarttime() and !$dbf->getEndtime()) {
                        if (!in_array($dateDbf->format("w"), [0, 6]) and !$dbf->getStarttime() and !$dbf->getEndtime()) {
                            $user->addDbf($dbf);
                            $entityManager->persist($user);
                        }
                    }
                }
                $entityManager->flush();

                foreach ($user->getDbfs() as $dbf) {
                    $abscence = current(array_filter(array_map(
                        fn ($abscence): ?Abscence => ($abscence->getDebut() <= $dbf->getAttDate() and $dbf->getAttDate() <= $abscence->getFin()) ? $abscence : null,
                        $user->getAbscences()->toArray()
                    )));
                    $conger = current(array_filter(array_map(
                        fn ($conger): ?Conger => ($conger->getDebut() <= $dbf->getAttDate() and $dbf->getAttDate() <= $conger->getFin()) ? $conger : null,
                        $user->getCongers()->toArray()
                    )));
                    $autorisationSortie = current(array_filter(array_map(
                        fn ($autorisationSortie): ?AutorisationSortie => ($autorisationSortie->getDateAutorisation() <= $dbf->getAttDate() and $dbf->getAttDate() <= $autorisationSortie->getDateAutorisation()) ? $autorisationSortie : null,
                        $user->getAutorisationSorties()->toArray()
                    )));
                    $pointage = new Pointage();
                    $pointage->setDate($dbf->getAttDate());
                    $pointage->setHoraire($this->horaireService->getHoraireForDate($pointage->getDate()));
                    if (!$dbf->getStarttime() and !$dbf->getEndtime() and !$conger and !$abscence) {
                        $abscence = new Abscence();
                        $abscence->setDebut($dbf->getAttDate());
                        $abscence->setFin($dbf->getAttDate());
                        $pointage->setAbscence($abscence);
                        $this->pointageService->setPointage($pointage);
                        $pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
                        $pointage->setTotaleRetard($this->pointageService->totalRetard());
                        $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
                        $pointage->setDiff($this->pointageService->diff());
                        //dd($pointage);
                        $entityManager->remove($dbf);
                        $user->addAbscence($abscence);
                        $user->addPointage($pointage);
                    } else if (!$dbf->getStarttime() and !$dbf->getEndtime() and $conger and !$conger->getDemiJourner()) {
                        $pointage->setCongerPayer($conger ? $conger : null);
                        dd($pointage);
                        $entityManager->remove($dbf);
                        $user->addPointage($pointage);
                    } else if ($dbf->getStarttime() and $dbf->getEndtime() /* and !$conger and !$autorisationSortie */) {
                        $pointage->setCongerPayer($conger ? $conger : null);
                        $pointage->setAutorisationSortie($autorisationSortie ? $autorisationSortie : null);
                        $pointage->setEntrer($dbf->getStarttime());
                        $pointage->setSortie($dbf->getEndtime());
                        $this->pointageService->setPointage($pointage);
                        $pointage->setNbrHeurTravailler($this->pointageService->nbrHeurTravailler());
                        $pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
                        $pointage->setTotaleRetard($this->pointageService->totalRetard());
                        $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
                        $pointage->setDiff($this->pointageService->diff());
                        /*$pointage->setDepartAnticiper(null);
                        $pointage->setRetardMidi(null);*/
                        $entityManager->remove($dbf);
                        $user->addPointage($pointage);
                    }
                }

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

                $entityManager->flush();
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
