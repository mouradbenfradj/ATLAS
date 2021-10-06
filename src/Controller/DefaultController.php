<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Service\PointageService;
use App\Repository\PointageRepository;
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
     * @Route("/", name="default")
     */
    public function index(PointageRepository $pointageRepository, Security $security, PointageService $pointageService): Response
    {
        $pointages = $pointageRepository->findBy(["employer" => $security->getUser()], ["date" => "ASC"]);
        return $this->render('default/index.html.twig', [
            'bilan' => $pointageService->getBilanGeneral($pointages)
        ]);
    }
}
