<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

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
            IntegerField::new('userID'),
            IntegerField::new('badgenumbe'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('qualification'),
            IntegerField::new('matricule'),
            DateField::new('debutTravaille'),
            DateField::new('demission'),
            NumberField::new('soldConger')->onlyOnIndex(),
            TimeField::new('soldAutorisationSortie')->onlyOnIndex(),
            BooleanField::new('isVerified'),

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $dbfGenerator = Action::new('dbfGenerator', 'generate depui DbF File')->linkToRoute('dbf_upload', function (User $user): array {
            return [
                'employer' => $user->getId()
            ];
        });
        $xlsxGenerator = Action::new('xlsxGenerator', 'generate depui Xlsx File')->linkToRoute('xlsx_upload', function (User $user): array {
            return [
                'employer' => $user->getId()
            ];
        });
        $actions->add(Crud::PAGE_INDEX, $dbfGenerator);
        return $actions->add(Crud::PAGE_INDEX, $xlsxGenerator);
    }
}
