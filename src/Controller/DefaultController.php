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
     * index
     * @Route("/")
     *
     * @param PointageService $pointageService
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
    /**
     * default
     * @Route("/", name="default")
     *
     * @param PointageService $pointageService
     * @return Response
     */
    public function default(BilanService $bilanService): Response
    {
        /**
         * @var User $employer
         */
        $employer = $this->getUser();
        if ($employer and property_exists($employer, 'pointages')) {
            $bilans = $bilanService->getBilanGeneral($employer->getPointages()->toArray());
        } else {
            $bilans = [];
        }
        return $this->render('default/index.html.twig', [
            'bilan' => $bilans
        ]);
    }
}
