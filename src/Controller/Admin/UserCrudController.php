<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email'),
            ChoiceField::new('roles')
                ->allowMultipleChoices()
                ->autocomplete()
                ->setChoices(
                    [
                        'User' => 'ROLE_USER',
                        'Employer' => 'ROLE_Employer',
                        'Admin' => 'ROLE_ADMIN',
                        'SuperAdmin' => 'ROLE_SUPER_ADMIN'
                    ]
                ),
            TextField::new('password')->hideOnIndex(),
            IntegerField::new('userID'),
            IntegerField::new('badgenumbe'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('qualification'),
            IntegerField::new('matricule'),
            DateField::new('debutTravaille'),
            DateField::new('demission'),
            AssociationField::new('pointage'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $dbfGenerator = Action::new('dbfGenerator', 'generate depui DbF File')
            ->linkToRoute('dbf_upload', function (User $user): array {
                return [
                    'id' => $user->getId()
                ];
            });
        return $actions->add(Crud::PAGE_INDEX, $dbfGenerator);
    }
}
