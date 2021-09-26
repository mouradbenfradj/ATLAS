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
        $user = $security->getUser();
        $pointages = $pointageRepository->findBy(["employer" => $user], ["date" => "ASC"]);
        $collectSemaine = [];

        foreach ($pointages as $index => $pointage) {
            $pointageService->setPointage($pointage);
            if ($pointageService->nextIsWeek() and $index) {
                array_push($collectSemaine, $pointageService->getInitBilan());
                dd($collectSemaine);
            }
            dd($pointageService->getInitBilan());
        }
        return $this->render('pointage/bilanSemestiriel.html.twig', [
            'pointages' => $collectSemaine,
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
        $user = $security->getUser();
        $pointages = $pointageRepository->findBy(["employer" => $user], ["date" => "ASC"]);
        $collectSemaine = [];

        foreach ($pointages as $index => $pointage) {
            $pointageService->setPointage($pointage);
            if ($pointageService->nextIsWeek() and $index) {
                array_push($collectSemaine, $pointageService->getInitBilan());
                dd($collectSemaine);
            }
            dd($pointageService->getInitBilan());
        }
        return $this->render('pointage/bilanMensuel.html.twig', [
            'pointages' => $collectSemaine,
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
        return $this->render('pointage/bilanAnnuel.html.twig', [
            'bilan' => [$bilan],
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
