<?php

namespace App\Controller\Admin;

use App\Entity\Dbf;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class DbfCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dbf::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('employer'),
            NumberField::new('userid'),
            IntegerField::new('badgenumbe'),
            TextField::new('ssn'),
            TextField::new('username'),
            TextField::new('autosch'),
            DateField::new('attdate'),
            NumberField::new('schid'),
            TimeField::new('clockintim'),
            TimeField::new('clockoutti'),
            TimeField::new('starttime'),
            TimeField::new('endtime'),
            NumberField::new('workday'),
            NumberField::new('realworkda'),
            TimeField::new('late'),
            TimeField::new('early'),
            NumberField::new('absent'),
            TimeField::new('overtime'),
            TimeField::new('worktime'),
            TextField::new('exceptioni'),
            TextField::new('mustin'),
            TextField::new('mustout'),
            NumberField::new('deptid'),
            NumberField::new('sspedaynor'),
            NumberField::new('sspedaywee'),
            NumberField::new('sspedayhol'),
            TimeField::new('atttime'),
            ArrayField::new('attchktime'),
        ];
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
        ->add('id')
        ->add('employer')
        ->add('userid')
        ->add('badgenumbe')
        ->add('ssn')
        ->add('username')
        ->add('autosch')
        ->add('attdate')
        ->add('schid')
        ->add('clockintim')
        ->add('clockoutti')
        ->add('starttime')
        ->add('endtime')
        ->add('workday')
        ->add('realworkda')
        ->add('late')
        ->add('early')
        ->add('absent')
        ->add('overtime')
        ->add('worktime')
        ->add('exceptioni')
        ->add('mustin')
        ->add('mustout')
        ->add('deptid')
        ->add('sspedaynor')
        ->add('sspedaywee')
        ->add('sspedayhol')
        ->add('atttime')
        ->add('attchktime');
    }
}
