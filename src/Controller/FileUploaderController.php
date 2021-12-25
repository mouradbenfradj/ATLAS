<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadType;
use App\Implement\DbfImpl;
use App\Interfaces\FileUploaderInterface;
use App\Service\FileUploader;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use PhpParser\Node\Stmt\Break_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploaderController extends AbstractController
{
    /**
     * @Route("/file/uploader/{employer}", name="file_uploader")
     */
    public function upload(Request $request, User $employer, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('upload')->getData();
            switch ($file->guessExtension()) {
                case 'dbf':
                    $fileUploader = new  FileUploader(new DbfImpl);
                    break;
                default: break;
                
            }
     
            $fileUploader->upload($file);
            dd($fileUploader);
            //$dbfService->setEmployer($employer);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($employer);
            $manager->flush();
            $this->addFlash('success', 'upload DBF Successfully');
            
            $url = $adminUrlGenerator
                ->setController(DbfCrudController::class)
                ->setAction('index')
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('file_uploader/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
