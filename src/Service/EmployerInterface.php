<?php

namespace App\Service;

interface EmployerInterface
{
   public function demanderUnConger();
   public function demanderUneAutorisationDeSortie();
   public function modifierWorkTime();
   public function demissionner();
}
