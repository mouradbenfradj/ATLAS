<?php

namespace App\Controller\Admin;

use App\Entity\Horaire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class HoraireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Horaire::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('horaire'),
            DateField::new('dateDebut'),
            DateField::new('dateFin'),
            TimeField::new('heurDebutTravaille'),
            TimeField::new('heurFinTravaille'),
            TimeField::new('debutPauseMatinal'),
            TimeField::new('finPauseMatinal'),
            TimeField::new('debutPauseDejeuner'),
            TimeField::new('finPauseDejeuner'),
            TimeField::new('debutPauseMidi'),
            TimeField::new('finPauseMidi'),
            AssociationField::new('pointages'),
            AssociationField::new('workTimes'),
            TimeField::new('margeDuRetard'),
            AssociationField::new('xlsxes')
        ];
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('horaire')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('heurDebutTravaille')
            ->add('heurFinTravaille')
            ->add('debutPauseMatinal')
            ->add('finPauseMatinal')
            ->add('debutPauseDejeuner')
            ->add('finPauseDejeuner')
            ->add('debutPauseMidi')
            ->add('finPauseMidi')
            ->add('pointages')
            ->add('workTimes')
            ->add('margeDuRetard')
            ->add('xlsxes');
    }
}
