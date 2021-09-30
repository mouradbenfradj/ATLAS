<?php

namespace App\Controller\Admin;

use App\Entity\AutorisationSortie;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AutorisationSortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AutorisationSortie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('employer'),
            DateField::new('dateAutorisation'),
            TimeField::new('time'),
        ];
    }
}
