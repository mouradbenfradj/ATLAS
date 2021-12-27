<?php


namespace App\Interfaces;

interface FileFactoryInterface
{
    public function createPointage(): AbstractPointage;
    public function createXlsx(): AbstractXlsx;
    public function createDbf(): AbstractDbf;

}
