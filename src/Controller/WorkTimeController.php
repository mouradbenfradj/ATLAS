<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Form\WorkTimeType;
use App\Repository\HoraireRepository;
use App\Repository\WorkTimeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/work/time")
 */
class WorkTimeController extends AbstractController
{
    /**
     * @Route("/", name="work_time_index", methods={"GET"})
     */
    public function index(HoraireRepository $horaireRepository, WorkTimeRepository $workTimeRepository, Security $security): Response
    {
        $workTime  = [];
        $horaires = $horaireRepository->findAll();
        $workTimes = $workTimeRepository->findByEmployer($security->getUser());
        foreach ($horaires as $indexHoraire => $horaire) {
            array_push($workTime, $horaire);
            foreach ($workTimes as $wt) {
                if ($horaire->getHoraire() == $wt->getHoraire()->getHoraire()) {
                    if (!$wt->getHeurDebutTravaille())
                        $wt->setHeurDebutTravaille($horaire->getHeurDebutTravaille());
                    if (!$wt->getHeurFinTravaille())
                        $wt->setHeurFinTravaille($horaire->getHeurFinTravaille());
                    if (!$wt->getDebutPauseMatinal())
                        $wt->setDebutPauseMatinal($horaire->getDebutPauseMatinal());
                    if (!$wt->getFinPauseMatinal())
                        $wt->setFinPauseMatinal($horaire->getFinPauseMatinal());
                    if (!$wt->getDebutPauseDejeuner())
                        $wt->setDebutPauseDejeuner($horaire->getDebutPauseDejeuner());
                    if (!$wt->getFinPauseDejeuner())
                        $wt->setFinPauseDejeuner($horaire->getFinPauseDejeuner());
                    if (!$wt->getDebutPauseMidi())
                        $wt->setDebutPauseMidi($horaire->getDebutPauseMidi());
                    if (!$wt->getFinPauseMidi())
                        $wt->setFinPauseMidi($horaire->getFinPauseMidi());
                    array_push($workTime, $wt);
                }
            }
        }
        //dd($workTime);
        return $this->render('work_time/index.html.twig', [
            'work_times' => $workTime,
        ]);
    }

    /**
     * @Route("/new", name="work_time_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security): Response
    {
        $workTime = new WorkTime();
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $workTime->setEmployer($security->getUser());
            $entityManager->persist($workTime);
            $entityManager->flush();

            return $this->redirectToRoute('work_time_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work_time/new.html.twig', [
            'work_time' => $workTime,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="work_time_show", methods={"GET"})
     */
    public function show(WorkTime $workTime): Response
    {
        return $this->render('work_time/show.html.twig', [
            'work_time' => $workTime,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="work_time_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, WorkTime $workTime): Response
    {
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('work_time_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work_time/edit.html.twig', [
            'work_time' => $workTime,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="work_time_delete", methods={"POST"})
     */
    public function delete(Request $request, WorkTime $workTime): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workTime->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($workTime);
            $entityManager->flush();
        }

        return $this->redirectToRoute('work_time_index', [], Response::HTTP_SEE_OTHER);
    }
}
