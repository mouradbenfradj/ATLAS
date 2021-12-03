<?php

namespace App\Controller;

use App\Entity\Pointage;
use App\Form\PointageType;
use App\Repository\PointageRepository;
use App\Service\BilanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/{_locale}/pointage")
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
     * @param Security $security
     * @param BilanService $bilanService
     * @return Response
     */
    public function bilanSemestiriel(Security $security, BilanService $bilanService): Response
    {
        return $this->render('pointage/bilanSemestiriel.html.twig', [
            'bilan' => $bilanService->getBilanSemestriel($security->getUser()->getPointages()->toArray()),
        ]);
    }
    /**
     * @Route("/bilanMensuel", name="bilan_mensuel", methods={"GET"})
     *
     * @param Security $security
     * @param BilanService $bilanService
     * @return Response
     */
    public function bilanMensuel(Security $security, BilanService $bilanService): Response
    {

        return $this->render('pointage/bilanMensuel.html.twig', [
            'bilan' =>  $bilanService->getBilanMensuel($security->getUser()->getPointages()->toArray()),
        ]);
    }
    /**
     * @Route("/bilanAnnuel", name="bilan_annuel", methods={"GET"})
     * @param Security $security
     * @param BilanService $bilanService
     * @return Response
     */
    public function bilanAnnuel(Security $security, BilanService $bilanService): Response
    {
        return $this->render('pointage/bilanAnnuel.html.twig', [
            'bilan' =>  $bilanService->getBilanAnnuel($security->getUser()->getPointages()->toArray()),
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
