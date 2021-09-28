<?php

namespace App\Controller\Admin;

use App\Entity\AutorisationSortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AutorisationSortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutorisationSortie::class;
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
