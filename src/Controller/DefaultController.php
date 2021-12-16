<?php

namespace App\Controller;

use App\Service\PointageService;
use App\Repository\PointageRepository;
use App\Service\BilanService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * Index
     * @Route("/", name="default")
     *
     * @param BilanService $bilanService
     * @return Response
     */
    public function index(BilanService $bilanService): Response
    {
        $bilanService->setEmployer($this->getUser());
        $bilans = $bilanService->getBilanGeneral();
        return $this->render('default/index.html.twig', [
            'bilan' => $bilans
        ]);
    }
}
