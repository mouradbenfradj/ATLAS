<?php

namespace App\Controller\Admin;

use App\Entity\WorkTime;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WorkTimeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WorkTime::class;
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
