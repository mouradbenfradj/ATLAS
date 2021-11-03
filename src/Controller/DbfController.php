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
    public function upload(
        Request $request,
        User $user
    ): Response {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $dbfs = new TableReader($dbf);
                $inDB = $this->pointageGeneratorService->dateInDB($user);
                while ($record = $dbfs->nextRecord()) {
                    $dateDbf = $this->dateService->dateToStringY_m_d($record->attdate);
                    $isJourFerier = $this->jourFerierService->isJourFerier($dateDbf);
                    if (!$isJourFerier and !in_array($dateDbf, $inDB)) {
                        $conger = $this->congerService->getIfConger($dateDbf, $user);
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
                        $user->addPointage($pointage);
                    }
                }

                $this->getDoctrine()->getManager()->flush();
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
