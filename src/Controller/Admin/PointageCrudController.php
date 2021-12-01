<?php

namespace App\Controller\Admin;

use App\Entity\Pointage;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PointageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pointage::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('employer'),
            DateField::new('date')->setRequired(true),
            AssociationField::new('horaire')->setRequired(true),
            TimeField::new('entrer')->setRequired(true),
            TimeField::new('sortie')->setRequired(true),
            TimeField::new('nbrHeurTravailler')->onlyOnIndex(),
            TimeField::new('retardEnMinute'),
            TimeField::new('departAnticiper'),
            TimeField::new('retardMidi'),
            TimeField::new('totaleRetard')->onlyOnIndex(),
            AssociationField::new('autorisationSortie'),
            AssociationField::new('congerPayer'),
            AssociationField::new('absence'),
            TimeField::new('heurNormalementTravailler')->onlyOnIndex(),
            TimeField::new('diff')->onlyOnIndex(),
        ];
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('date')
            ->add('horaire')
            ->add('entrer')
            ->add('sortie')
            ->add('nbrHeurTravailler')
            ->add('retardEnMinute')
            ->add('departAnticiper')
            ->add('retardMidi')
            ->add('totaleRetard')
            ->add('autorisationSortie')
            ->add('congerPayer')
            ->add('absence')
            ->add('heurNormalementTravailler')
            ->add('diff')
            ->add('employer');
    }
}
