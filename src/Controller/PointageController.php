<?php

namespace App\Controller;

use App\Entity\Pointage;
use App\Form\PointageType;
use App\Repository\PointageRepository;
use App\Service\PointageService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/pointage")
 */
class PointageController extends AbstractController
{
    /**
     * @Route("/", name="pointage_index", methods={"GET"})
     */
    public function index(PointageRepository $pointageRepository): Response
    {
        return $this->render('pointage/index.html.twig', [
            'pointages' => $pointageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/bilanSemestiriel", name="bilan_semestiriel", methods={"GET"})
     * 
     * @param PointageRepository $pointageRepository
     * @param Security $security
     * @param PointageService $pointageService
     * @return Response
     */
    public function bilanSemestiriel(PointageRepository $pointageRepository, Security $security, PointageService $pointageService): Response
    {
        $pointages = $pointageRepository->findBy(["employer" => $security->getUser()], ["date" => "ASC"]);
        $bilan = $pointageService->getInitBilan();
        $thisWeek = 0;
        $countWeek = 1;
        $collectSemaine = [];
        foreach ($pointages as $index => $pointage) {
            if ($thisWeek != $pointage->getDate()->format('W')) {
                if ($thisWeek) {
                    array_push($collectSemaine, $bilan);
                    $countWeek++;
                } else {
                    $countWeek = $pointage->getDate()->format('W');
                }
                $thisWeek = $pointage->getDate()->format('W');
                $bilan = $pointageService->getInitBilan();
                $bilan["interval"] = $countWeek;
            }

            $bilan["nbrHeurTravailler"] = $pointageService->bilan($pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
            if ($pointage->getRetardEnMinute())
                $bilan["retardEnMinute"] = $pointageService->bilan($pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
            if ($pointage->getDepartAnticiper())
                $bilan["departAnticiper"] = $pointageService->bilan($pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
            if ($pointage->getRetardMidi())
                $bilan["retardMidi"] = $pointageService->bilan($pointage->getRetardMidi(), $bilan["retardMidi"]);
            $bilan["totaleRetard"] = $pointageService->bilan($pointage->getTotaleRetard(), $bilan["totaleRetard"]);
            if ($pointage->getAutorisationSortie())
                $bilan["autorisationSortie"] = $pointageService->bilan($pointage->getAutorisationSortie()->getTime(), $bilan["autorisationSortie"]);
            if ($pointage->getCongerPayer()) {
                if ($pointage->getCongerPayer()->getDemiJourner()) {
                    $bilan["congerPayer"] += 0.5;
                } else {
                    $bilan["congerPayer"] += 1;
                }
            }
            $bilan["abscence"] += $pointage->getAbscence();
            $bilan["heurNormalementTravailler"] = $pointageService->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
            $bilan["diff"] = $pointageService->bilan($pointage->getDiff(), $bilan["diff"]);
        }
        array_push($collectSemaine, $bilan);
        return $this->render('pointage/bilanSemestiriel.html.twig', [
            'bilan' => $collectSemaine,
        ]);
    }
    /**
     * @Route("/bilanMensuel", name="bilan_mensuel", methods={"GET"})
     * 
     * @param PointageRepository $pointageRepository
     * @param Security $security
     * @param PointageService $pointageService
     * @return Response
     */
    public function bilanMensuel(PointageRepository $pointageRepository, Security $security, PointageService $pointageService): Response
    {
        $pointages = $pointageRepository->findBy(["employer" => $security->getUser()], ["date" => "ASC"]);
        $bilan = $pointageService->getInitBilan();
        $thisYear = 0;
        $thisMonth = 0;
        $collectSemaine = [];
        foreach ($pointages as $index => $pointage) {

            if ($thisYear . '-' . $thisMonth != $pointage->getDate()->format('Y-m')) {
                if ($thisYear and $thisMonth)
                    array_push($collectSemaine, $bilan);
                $thisYear =  $pointage->getDate()->format('Y');
                $thisMonth =  $pointage->getDate()->format('m');
                $bilan = $pointageService->getInitBilan();
                $bilan["interval"] =  $pointage->getDate()->format('Y-m');
            }

            $bilan["nbrHeurTravailler"] = $pointageService->bilan($pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
            if ($pointage->getRetardEnMinute())
                $bilan["retardEnMinute"] = $pointageService->bilan($pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
            if ($pointage->getDepartAnticiper())
                $bilan["departAnticiper"] = $pointageService->bilan($pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
            if ($pointage->getRetardMidi())
                $bilan["retardMidi"] = $pointageService->bilan($pointage->getRetardMidi(), $bilan["retardMidi"]);
            $bilan["totaleRetard"] = $pointageService->bilan($pointage->getTotaleRetard(), $bilan["totaleRetard"]);
            if ($pointage->getAutorisationSortie())
                $bilan["autorisationSortie"] = $pointageService->bilan($pointage->getAutorisationSortie()->getTime(), $bilan["autorisationSortie"]);
            if ($pointage->getCongerPayer()) {
                if ($pointage->getCongerPayer()->getDemiJourner()) {
                    $bilan["congerPayer"] += 0.5;
                } else {
                    $bilan["congerPayer"] += 1;
                }
            }
            $bilan["abscence"] += $pointage->getAbscence();
            $bilan["heurNormalementTravailler"] = $pointageService->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
            $bilan["diff"] = $pointageService->bilan($pointage->getDiff(), $bilan["diff"]);
        }
        array_push($collectSemaine, $bilan);
        return $this->render('pointage/bilanMensuel.html.twig', [
            'bilan' => $collectSemaine,
        ]);
    }
    /**
     * @Route("/bilanAnnuel", name="bilan_annuel", methods={"GET"})
     */
    public function bilanAnnuel(PointageRepository $pointageRepository, Security $security, PointageService $pointageService): Response
    {
        $pointages = $pointageRepository->findBy(["employer" => $security->getUser()], ["date" => "ASC"]);
        $bilan = $pointageService->getInitBilan();
        $thisYear = 0;
        $collectAnnuel = [];
        foreach ($pointages as $index => $pointage) {

            if ($thisYear != $pointage->getDate()->format('Y')) {
                if ($thisYear)
                    array_push($collectAnnuel, $bilan);
                $thisYear =  $pointage->getDate()->format('Y');
                $bilan = $pointageService->getInitBilan();

                $bilan["interval"] =  $pointage->getDate()->format('Y');
            }

            $bilan["nbrHeurTravailler"] = $pointageService->bilan($pointage->getNbrHeurTravailler(), $bilan["nbrHeurTravailler"]);
            if ($pointage->getRetardEnMinute())
                $bilan["retardEnMinute"] = $pointageService->bilan($pointage->getRetardEnMinute(), $bilan["retardEnMinute"]);
            if ($pointage->getDepartAnticiper())
                $bilan["departAnticiper"] = $pointageService->bilan($pointage->getDepartAnticiper(), $bilan["departAnticiper"]);
            if ($pointage->getRetardMidi())
                $bilan["retardMidi"] = $pointageService->bilan($pointage->getRetardMidi(), $bilan["retardMidi"]);
            $bilan["totaleRetard"] = $pointageService->bilan($pointage->getTotaleRetard(), $bilan["totaleRetard"]);
            if ($pointage->getAutorisationSortie())
                $bilan["autorisationSortie"] = $pointageService->bilan($pointage->getAutorisationSortie()->getTime(), $bilan["autorisationSortie"]);
            if ($pointage->getCongerPayer()) {
                if ($pointage->getCongerPayer()->getDemiJourner()) {
                    $bilan["congerPayer"] += 0.5;
                } else {
                    $bilan["congerPayer"] += 1;
                }
            }
            $bilan["abscence"] += $pointage->getAbscence();
            $bilan["heurNormalementTravailler"] = $pointageService->bilan($pointage->getHeurNormalementTravailler(), $bilan["heurNormalementTravailler"]);
            $bilan["diff"] = $pointageService->bilan($pointage->getDiff(), $bilan["diff"]);
        }
        array_push($collectAnnuel, $bilan);

        return $this->render('pointage/bilanAnnuel.html.twig', [
            'bilan' => $collectAnnuel,
        ]);
    }

    /**
     * @Route("/new", name="pointage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pointage = new Pointage();
        $form = $this->createForm(PointageType::class, $pointage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pointage);
            $entityManager->flush();

            return $this->redirectToRoute('pointage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pointage/new.html.twig', [
            'pointage' => $pointage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="pointage_show", methods={"GET"})
     */
    public function show(Pointage $pointage): Response
    {
        return $this->render('pointage/show.html.twig', [
            'pointage' => $pointage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pointage_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pointage $pointage): Response
    {
        $form = $this->createForm(PointageType::class, $pointage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pointage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pointage/edit.html.twig', [
            'pointage' => $pointage,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="pointage_delete", methods={"POST"})
     */
    public function delete(Request $request, Pointage $pointage): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pointage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pointage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pointage_index', [], Response::HTTP_SEE_OTHER);
    }
}
