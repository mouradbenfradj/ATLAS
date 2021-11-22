<?php

namespace App\Controller;

use App\Service\PointageService;
use App\Repository\PointageRepository;
use App\Service\BilanService;
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
     * index
     * @Route("/", name="default")
     *
     * @param Security $security
     * @param PointageService $pointageService
     * @return Response
     */
    public function index(Security $security, BilanService $bilanService): Response
    {
        /**
         * @var User $employer
         */
        $employer = $security->getUser();
        return $this->render('default/index.html.twig', [
            'bilan' => $bilanService->getBilanGeneral($employer->getPointages()->toArray())
        ]);
    }
}
