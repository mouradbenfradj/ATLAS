<?php

namespace App\Controller\Admin;

use App\Entity\Xlsx;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class XlsxCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Xlsx::class;
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
