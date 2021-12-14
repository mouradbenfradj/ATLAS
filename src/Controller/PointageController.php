<?php

namespace App\Controller;

use App\Controller\Admin\PointageCrudController;
use App\Entity\Pointage;
use App\Form\PointageType;
use App\Repository\DbfRepository;
use App\Repository\PointageRepository;
use App\Service\BilanService;
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
     * upload function
     * @Route("/upload", name="xlsx_upload_from_dbf", methods={"GET","POST"})
     *
     * @param Request $request
     * @param User $employer
     * @return Response
     */
    public function upload(Request $request, DbfRepository $dbfRepository): Response
    {
        foreach ($dbfRepository->findAll() as $dbf) {
        }
               
        /* dd($user);
        $user->setSoldConger($this->employerService->calculerSoldConger($user));
        $user->setSoldAutorisationSortie($this->employerService->calculerAS($user)); */
        /*     $manager = $this->getDoctrine()->getManager();
            $manager->persist($employer);
            $manager->flush(); */
        $this->addFlash('success', 'upload DBF Successfully');
        $url = $this->adminUrlGenerator
                ->setController(PointageCrudController::class)
                ->setAction('index')
                ->generateUrl();
        return $this->redirect($url);

        /*  return $this->render('dbf/upload.html.twig', [
             'form' => $form->createView(),
         ]); */
    }

    /**
     * @Route("/bilanSemestiriel", name="bilan_semestiriel", methods={"GET"})
     *
     * @param BilanService $bilanService
     * @return Response
     */
    public function bilanSemestiriel(BilanService $bilanService): Response
    {
        return $this->render('pointage/bilanSemestiriel.html.twig', [
            'bilan' => $bilanService->getBilanSemestriel($this->getUser()->getPointages()->toArray()),
        ]);
    }
    /**
     * @Route("/bilanMensuel", name="bilan_mensuel", methods={"GET"})
     *
     * @param BilanService $bilanService
     * @return Response
     */
    public function bilanMensuel(BilanService $bilanService): Response
    {
        return $this->render('pointage/bilanMensuel.html.twig', [
            'bilan' =>  $bilanService->getBilanMensuel($this->getUser()->getPointages()->toArray()),
        ]);
    }
    /**
     * @Route("/bilanAnnuel", name="bilan_annuel", methods={"GET"})
     * @param BilanService $bilanService
     * @return Response
     */
    public function bilanAnnuel(BilanService $bilanService): Response
    {
        return $this->render('pointage/bilanAnnuel.html.twig', [
            'bilan' =>  $bilanService->getBilanAnnuel($this->getUser()->getPointages()->toArray()),
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
