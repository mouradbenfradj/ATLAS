<?php

namespace App\Controller;

use App\Controller\Admin\DbfCrudController;
use App\Entity\User;
use App\Form\UploadType;
use App\Service\DbfService;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DbfController extends AbstractController
{
    /**
     * @Route("/dbf/file/uploader/{employer}", name="dbf_uploader")
    */
    public function upload(DbfService $dbfService, ManagerRegistry $doctrine, User $employer, AdminUrlGenerator $adminUrlGenerator, Request $request): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('upload')->getData();
            $employer= $dbfService->upload($employer, $file);
            $manager = $doctrine->getManager();
            $manager->persist($employer);
            $manager->flush();
            $this->addFlash('success', 'upload DBF Successfully');
            $url = $adminUrlGenerator
                  ->setController(DbfCrudController::class)
                  ->setAction('index')
                  ->generateUrl();
            return $this->redirect($url);
        }
        return $this->render('fileUploader/upload.html.twig', [
              'form' => $form->createView(),
          ]);
    }
}
