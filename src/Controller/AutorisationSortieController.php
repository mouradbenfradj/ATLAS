<?php

namespace App\Controller;

use App\Entity\AutorisationSortie;
use App\Form\AutorisationSortieType;
use App\Repository\AutorisationSortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/autorisation/sortie")
 */
class AutorisationSortieController extends AbstractController
{
    /**
     * @Route("/", name="autorisation_sortie_index", methods={"GET"})
     */
    public function index(AutorisationSortieRepository $autorisationSortieRepository): Response
    {
        return $this->render('autorisation_sortie/index.html.twig', [
            'autorisation_sorties' => $autorisationSortieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="autorisation_sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $autorisationSortie = new AutorisationSortie();
        $form = $this->createForm(AutorisationSortieType::class, $autorisationSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($autorisationSortie);
            $entityManager->flush();

            return $this->redirectToRoute('autorisation_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('autorisation_sortie/new.html.twig', [
            'autorisation_sortie' => $autorisationSortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="autorisation_sortie_show", methods={"GET"})
     */
    public function show(AutorisationSortie $autorisationSortie): Response
    {
        return $this->render('autorisation_sortie/show.html.twig', [
            'autorisation_sortie' => $autorisationSortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="autorisation_sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AutorisationSortie $autorisationSortie): Response
    {
        $form = $this->createForm(AutorisationSortieType::class, $autorisationSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('autorisation_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('autorisation_sortie/edit.html.twig', [
            'autorisation_sortie' => $autorisationSortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="autorisation_sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, AutorisationSortie $autorisationSortie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $autorisationSortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($autorisationSortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('autorisation_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}
