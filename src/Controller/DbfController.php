<?php

namespace App\Controller;

use XBase\TableReader;
use App\Form\UploadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\PointageCrudController;
use App\Entity\User;
use App\Service\DateService;
use App\Service\JourFerierService;
use App\Service\PointageGeneratorService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{

    /**
     * adminUrlGenerator
     *
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;

    /**
     * __construct
     *
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return void
     */
    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * index
     * @Route("/", name="dbf")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('dbf/index.html.twig', [
            'controller_name' => 'DbfController',
        ]);
    }


    /**
     * upload
     * @Route("/upload/{user}", name="dbf_upload", methods={"GET","POST"})
     *
     * @param Request $request
     * @param DateService $dateService
     * @param JourFerierService $jourFerierService
     * @param PointageGeneratorService $pointageGeneratorService
     * @param User $user
     * @return Response
     */
    public function upload(
        Request $request,
        DateService $dateService,
        JourFerierService $jourFerierService,
        PointageGeneratorService $pointageGeneratorService,
        User $user
    ): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $dbfs = new TableReader($dbf);
                while ($record = $dbfs->nextRecord()) {
                    $dateDbf = $dateService->dateToStringY_m_d($record->attdate);
                    $isJourFerier = $jourFerierService->isJourFerier($dateDbf);
                    $inDB = $pointageGeneratorService->dateInDB($user);

                    if (!$isJourFerier and !in_array($dateDbf, $inDB)) {
                        $user->addPointage($pointageGeneratorService->fromDbfFile($record));
                    }
                }
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'id.updated_successfully');
            }
            $url = $this->adminUrlGenerator
                ->setController(PointageCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('dbf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
