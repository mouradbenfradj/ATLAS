<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Service\PointageService;
use App\Repository\PointageRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(PointageRepository $pointageRepository, Security $security, PointageService $pointageService): Response
    {
        $pointages = $pointageRepository->findBy(["employer" => $security->getUser()], ["date" => "ASC"]);
        $bilan = $pointageService->getInitBilan();
        $thisYear = 0;
        foreach ($pointages as $index => $pointage) {
            if ($thisYear != $pointage->getDate()->format('Y')) {
                $thisYear =  $pointage->getDate()->format('Y');
                $bilan["interval"] = $bilan["interval"] + 1;
            }

            $bilan["nbrHeurTravailler"] = $pointageService->bilan($pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
            $bilan["retardEnMinute"] = $pointageService->bilan($pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
            if ($pointage->getDepartAnticiper())
                $bilan["departAnticiper"] = $pointageService->bilan($pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
            if ($pointage->getRetardMidi())
                $bilan["retardMidi"] = $pointageService->bilan($pointage->getRetardMidi(), $bilan["retardMidi"]);
            $bilan["totaleRetard"] = $pointageService->bilan($pointage->getTotaleRetard(), $bilan["totaleRetard"]);
            if ($pointage->getAutorisationSortie())
                $bilan["autorisationSortie"] = $pointageService->bilan($pointage->getAutorisationSortie(), $bilan["autorisationSortie"]);
            $bilan["congerPayer"] += $pointage->getCongerPayer();
            $bilan["abscence"] += $pointage->getAbscence();
            $bilan["heurNormalementTravailler"] = $pointageService->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
            $bilan["diff"] = $pointageService->bilan($pointage->getDiff(), $bilan["diff"]);
        }
        /*
        $formatted = sprintf('%02d:%02d:%02d', ($bilan["heurNormalementTravailler"] / 3600), ($bilan["heurNormalementTravailler"] / 60 % 60),  $bilan["heurNormalementTravailler"] % 60);

        echo $formatted; // Outputs 35:04:28
        die();*/
        return $this->render('default/index.html.twig', [
            'bilan' => [$bilan],
        ]);
    }
}
