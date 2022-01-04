<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('file_uploader/index.html.twig', [
            'bilan' => []
        ]);
    }


    /**
     * @Route("/upload", name="upload_dbf")
     */
    public function upload(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            $employer = $fileUploader->upload($dbf);
            $manager = $doctrine->getManager();
            $manager->persist($employer);
            $manager->flush();
            $this->addFlash('success', 'upload DBF Successfully');
            
            $url = $this->adminUrlGenerator
                ->setController(DbfCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }
        
        return $this->render('file_uploader/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}