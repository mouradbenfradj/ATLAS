<?php

namespace App\Controller\Admin;

use App\Entity\Abscence;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AbscenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Abscence::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
