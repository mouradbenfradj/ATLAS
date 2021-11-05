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
    private $congerService;
    private $horaireService;
    private $pointageService;
    private $flash;
    private $autorisationSortieService;
    /**
     * timeService
     *
     * @var TimeService
     */
    private $timeService;

    /**
     * __construct
     *
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return void
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
        AutorisationSortieService $autorisationSortieService
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
    public function upload(Request $request, User $user, DbfRepository $dbfRepository): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $entityManager = $this->getDoctrine()->getManager();
                $dateDbfInDb = array_map(
                    fn ($date): string => $date->getAttdate()->format('Y-m-d'),
                    $user->getDbfs()->toArray()
                );
                $dbfs = new TableReader($dbf);
                $datePointageInDB = $this->pointageGeneratorService->dateInDB($user);
                $inDb = array_merge($dateDbfInDb, $datePointageInDB);

                //$inDB = $this->pointageGeneratorService->dateInDB($user);
                while ($record = $dbfs->nextRecord()) {
                    //$dateDbf = $this->dateService->dateToStringY_m_d($record->attdate);
                    $dateDbf = $this->dateService->dateString_d_m_Y_ToDateTime($record->attdate);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateDbf->format("Y-m-d"));
                    if (!$isJourFerier and !in_array($dateDbf->format('Y-m-d'), $inDb)) {
                        $dbf = new Dbf();
                        $dbf->setUserid($record->userid);
                        $dbf->setBadgenumbe(intval($record->badgenumbe));
                        $dbf->setSsn($record->ssn);
                        $dbf->setUsername($record->username);
                        $dbf->setAutosch($record->autosch);
                        $dbf->setAttdate($dateDbf);
                        $dbf->setSchid($record->schid);
                        $dbf->setClockintim($this->timeService->timeStringToDateTime($record->clockintim));
                        $dbf->setClockoutti($this->timeService->timeStringToDateTime($record->clockoutti));
                        $dbf->setStarttime($this->timeService->timeStringToDateTime($record->starttime));
                        $dbf->setEndtime($this->timeService->timeStringToDateTime($record->endtime));
                        $dbf->setWorkday($record->workday);
                        $dbf->setRealworkda($record->realworkda);
                        $dbf->setLate($this->timeService->timeStringToDateTime($record->late));
                        $dbf->setEarly($this->timeService->timeStringToDateTime($record->early));
                        $dbf->setAbsent($record->absent);
                        $dbf->setOvertime($this->timeService->timeStringToDateTime($record->overtime));
                        $dbf->setWorktime($this->timeService->timeStringToDateTime($record->worktime));
                        $dbf->setExceptioni($record->exceptioni);
                        $dbf->setMustin($record->mustin);
                        $dbf->setMustout($record->mustout);
                        $dbf->setDeptid($record->deptid);
                        $dbf->setSspedaynor($record->sspedaynor);
                        $dbf->setSspedaywee($record->sspedaywee);
                        $dbf->setSspedayhol($record->sspedayhol);
                        $dbf->setAtttime($this->timeService->timeStringToDateTime($record->atttime));;
                        $dbf->setAttchktime(explode(" ", $record->attchktime));
                        $dbf->setEmployer($user);
                        $user->addDbf($dbf);
                        $entityManager->persist($user);
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
                        $entityManager->remove($dbf);
                    } else if (!$dbf->getStarttime() and !$dbf->getEndtime() and $conger and !$conger->getDemiJourner()) {
                        $pointage->setCongerPayer($conger ? $conger : null);
                        $entityManager->remove($dbf);
                    } else if ($dbf->getStarttime() and $dbf->getEndtime() /* and !$conger and !$autorisationSortie */) {
                        $pointage->setCongerPayer($conger ? $conger : null);
                        $pointage->setAutorisationSortie($autorisationSortie ? $autorisationSortie : null);
                        $pointage->setEntrer($dbf->getStarttime());
                        $pointage->setSortie($dbf->getEndtime());


                        $this->pointageService->setPointage($pointage);

                        $pointage->setNbrHeurTravailler($this->pointageService->nbrHeurTravailler());
                        $pointage->setRetardEnMinute($this->pointageService->retardEnMinute());
                        /*  if ($record->starttime != "" and $this->timeService->isTimeHi($record->starttime))
                        else {
                            $pointage->setEntrer(new DateTime("00:00:00"));
                            $this->flash->add('danger ', 'saisie automatique de l\'heur d\'entrer a 00:00:00 pour la date ' . $record->attdate);
                        } */
                        /* if ($record->endtime != ""  and $this->timeService->isTimeHi($record->endtime))
                            $pointage->setSortie(new DateTime($record->endtime));
                        else {
                            $pointage->setSortie(new DateTime("23:00:00"));
                            $this->flash->add('danger ', 'saisie automatique de l\'heur de sortie a 23:00:00 pour la date ' . $record->attdate);
                        } */


                        /*
                        $pointage->setDepartAnticiper(null);
                        $pointage->setRetardMidi(null);
                        $pointage->setTotaleRetard($this->pointageService->totalRetard());
                        $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
                        $pointage->setDiff($this->pointageService->diff()); */
                        $entityManager->remove($dbf);
                    }
                    $user->addPointage($pointage);
                }

                dd($user->getPointages()->toArray());





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
