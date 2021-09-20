<?php

namespace App\Controller;

use App\Entity\Pointage;
use App\Form\UploadType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use XBase\TableReader;

/**
 * @Route("/dbf")
 */
class DbfController extends AbstractController
{
    /**
     * @Route("/", name="dbf")
     */
    public function index(): Response
    {
        return $this->render('dbf/index.html.twig', [
            'controller_name' => 'DbfController',
        ]);
    }


    /**
     * @Route("/upload/{id}", name="dbf_upload", methods={"GET","POST"})
     */
    public function upload(Request $request, $id): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbf = $form->get('upload')->getData();
            if ($dbf) {
                $table = new TableReader($dbf);
                while ($record = $table->nextRecord()) {
                    $pointage = new Pointage();
                    $pointage->setDate(new DateTime($record->attdate));
                    $pointage->setEntrer(new DateTime($record->starttime));
                    $pointage->setSortie(new DateTime($record->endtime));
                    dump($record->attdate);
                    /*  $record->userid;
                    $record->badgenumbe;
                    $record->ssn;
                    $record->username;
                    $record->autosch;
                    $record->attdate;
                    $record->schid;
                    $record->clockintim;
                    $record->clockoutti;
                    $record->;
                    $record->;
                    $record->workday;
                    $record->realworkda;
                    $record->late;
                    $record->early;
                    $record->absent;
                    $record->overtime;
                    $record->worktime;
                    $record->exceptioni;
                    $record->mustin;
                    $record->mustout;
                    $record->deptid;
                    $record->sspedaynor;
                    $record->sspedaywee;
                    $record->sspedayhol;
                    $record->atttime;
                    $record->attchktime; */
                }
                dd('ff');
            }
            return $this->redirectToRoute('dbf_index', [], Response::HTTP_SEE_OTHER);
        }
        dump($id);
        return $this->render('dbf/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
