<?php

namespace App\Controller;

use App\Entity\Conger;
use App\Form\CongerType;
use App\Repository\CongerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/{_locale}/conger")
 */
class CongerController extends AbstractController
{
    /**
     * @Route("/", name="conger_index", methods={"GET"})
     */
    public function index(CongerRepository $congerRepository): Response
    {
        return $this->render('conger/index.html.twig', [
            'congers' => $congerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="conger_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security): Response
    {
        $conger = new Conger();
        $form = $this->createForm(CongerType::class, $conger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $employer = $security->getUser();
            $conger->setEmployer($employer);
            $entityManager->persist($conger);
            $entityManager->flush();

            return $this->redirectToRoute('conger_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conger/new.html.twig', [
            'conger' => $conger,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="conger_show", methods={"GET"})
     */
    public function show(Conger $conger): Response
    {
        return $this->render('conger/show.html.twig', [
            'conger' => $conger,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="conger_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Conger $conger): Response
    {
        $form = $this->createForm(CongerType::class, $conger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conger_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('conger/edit.html.twig', [
            'conger' => $conger,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="conger_delete", methods={"POST"})
     */
    public function delete(Request $request, Conger $conger): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conger->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($conger);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conger_index', [], Response::HTTP_SEE_OTHER);
    }
}
