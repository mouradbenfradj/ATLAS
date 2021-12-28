<?php

namespace App\Controller\Admin;

use App\Entity\Dbf;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        // the names of the Doctrine entity properties where the search is made on
        // (by default it looks for in all properties)
        //->setSearchFields(['name', 'description'])
        // use dots (e.g. 'seller.email') to search in Doctrine associations
        //->setSearchFields(['name', 'description', 'seller.email', 'seller.address.zipCode'])
        // set it to null to disable and hide the search box
        //->setSearchFields(null)

        // defines the initial sorting applied to the list of entities
        // (user can later change this sorting by clicking on the table columns)
        //->setDefaultSort(['id' => 'DESC'])
        //->setDefaultSort(['id' => 'DESC', 'title' => 'ASC', 'startsAt' => 'DESC'])
        // you can sort by Doctrine associations up to two levels
        //->setDefaultSort(['seller.name' => 'ASC'])

        // the max number of entities to display per page
        ->setPaginatorPageSize(30)
        // the number of pages to display on each side of the current page
        // e.g. if num pages = 35, current page = 7 and you set ->setPaginatorRangeSize(4)
        // the paginator displays: [Previous]  1 ... 3  4  5  6  [7]  8  9  10  11 ... 35  [Next]
        // set this number to 0 to display a simple "< Previous | Next >" pager
        //->setPaginatorRangeSize(4)

        // these are advanced options related to Doctrine Pagination
        // (see https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html)
        //->setPaginatorUseOutputWalkers(true)
        //->setPaginatorFetchJoinCollection(true)
    ;
    }
}
