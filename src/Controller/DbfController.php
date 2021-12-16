<?php

namespace App\Controller;

use App\Controller\Admin\DbfCrudController;
use App\Entity\User;
use App\Form\UploadType;
use App\Service\DbfService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{
    
    //private $flash;
    /**
     * AdminUrlGenerator
     *
     * @var AdminUrlGenerator
     */
    private $adminUrlGenerator;
    public function __construct(/*FlashBagInterface $flash, */AdminUrlGenerator $adminUrlGenerator)
    {
        //$this->flash = $flash;
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
     * upload function
     * @Route("/upload/{employer}", name="dbf_upload", methods={"GET","POST"})
     *
     * @param Request $request
     * @param User $employer
     * @return Response
     */
    public function upload(Request $request, User $employer, DbfService $dbfService): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $dbfService->setEmployer($employer);
                $employer = $dbfService->installDbfFile($dbf);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($employer);
                $manager->flush();
                $this->addFlash('success', 'upload DBF Successfully');
            }
            $url = $this->adminUrlGenerator
                ->setController(DbfCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('dbf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
