<?php

namespace App\Controller\Admin;

use App\Entity\Conger;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CongerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conger::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('type'),
            AssociationField::new('employer'),
            DateField::new('debut'),
            DateField::new('fin'),
            BooleanField::new('demiJourner'),
        ];
    }
}
