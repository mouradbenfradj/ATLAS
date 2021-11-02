<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use XBase\TableReader;
use App\Entity\Pointage;
use App\Form\UploadType;
use App\Service\DateService;
use App\Service\JourFerierService;
use ContainerDqTIZIB\getCongerService;
use App\Service\PointageGeneratorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Service\CongerService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * __construct
     *
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return void
     */
    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
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
     * @param DateService $dateService
     * @param JourFerierService $jourFerierService
     * @param PointageGeneratorService $pointageGeneratorService
     * @param User $user
     * @return Response
     */
    public function upload(
        Request $request,
        DateService $dateService,
        JourFerierService $jourFerierService,
        PointageGeneratorService $pointageGeneratorService,
        CongerService $congerService,
        User $user
    ): Response {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $dbfs = new TableReader($dbf);
                $inDB = $pointageGeneratorService->dateInDB($user);
                while ($record = $dbfs->nextRecord()) {
                    $dateDbf = $dateService->dateToStringY_m_d($record->attdate);
                    $isJourFerier = $jourFerierService->isJourFerier($dateDbf);
                    if (!$isJourFerier and !in_array($dateDbf, $inDB)) {
                        $conger = $congerService->getIfConger($dateDbf, $user);
                        $pointage = new Pointage();
                        $pointage->setDate($this->dateService->dateString_d_m_Y_ToDateTime($record->attdate));
                        $pointage->setHoraire($this->horaireService->getHoraireForDate($pointage->getDate()));
                        if ($record->starttime != "" and  DateTime::createFromFormat('H:i', $record->starttime) !== false)
                            $pointage->setEntrer(new DateTime($record->starttime));
                        else {
                            $pointage->setEntrer(new DateTime("00:00:00"));
                            $this->flash->add('danger ', 'saisie automatique de l\'heur d\'entrer a 00:00:00 pour la date ' . $record->attdate);
                        }
                        if ($record->endtime != "" and  DateTime::createFromFormat('H:i', $record->endtime) !== false)
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
                        $pointage->setAutorisationSortie(null);
                        $pointage->setCongerPayer(null);
                        $pointage->setAbscence(null);
                        $pointage->setHeurNormalementTravailler($this->pointageService->heurNormalementTravailler());
                        $pointage->setDiff($this->pointageService->diff());
                        $user->addPointage($pointage);
                        /*
                                    $record->userid;
                                    $record->badgenumbe;
                                    $record->ssn;
                                    $record->username;
                                    $record->autosch;
                                    $record->attdate;
                                    $record->schid;
                                    $record->clockintim;
                                    $record->clockoutti;
                                    $record->;
                                    $record->;
                                    $record->workday;
                                    $record->realworkda;
                                    $record->late;
                                    $record->early;
                                    $record->absent;
                                    $record->overtime;
                                    $record->worktime;
                                    $record->exceptioni;
                                    $record->mustin;
                                    $record->mustout;
                                    $record->deptid;
                                    $record->sspedaynor;
                                    $record->sspedaywee;
                                    $record->sspedayhol;
                                    $record->atttime;
                                    $record->attchktime;
                                */
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
