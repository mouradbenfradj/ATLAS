<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadType;
use App\Service\UserService;
use App\Util\FileInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
   
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
      * @Route("/file/uploader/{employer}", name="file_uploader")
      */
    public function upload(Request $request, User $employer, AdminUrlGenerator $adminUrlGenerator, FileInterface $fileInterface): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fileUploader= $fileInterface->upload($form->get('upload')->getData());
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

        return $this->render('user/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
