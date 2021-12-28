<?php

namespace App\Controller\Admin;

use App\Entity\JourFerier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class JourFerierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JourFerier::class;
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
