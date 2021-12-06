<?php


use App\Entity\Dbf;
use App\Entity\Xlsx;

interface PointageInterface
{
    public function creerDepuisUnDbfFile(Dbf $dbf);
    public function creerDepuisUnXlsxFile(Xlsx $xlsx);
    public function mentrerLesDateDansLaBaseDeDonner();
}
