<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ATLAS5 4');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fa fa-id-card', EntityClass::class);
        /*  yield MenuItem::subMenu('bilan', 'fa fa-gavel')->setSubItems([
             MenuItem::linkToRoute('Bilan annuel', 'fa fa-thermometer-full', 'default'),
             MenuItem::linkToRoute('Bilan mensuel', 'fa fa-thermometer-half', 'default'),
             MenuItem::linkToRoute('Bilan semestiriel', 'fa fa-thermometer-quarter', 'default'),
         ]); */
        yield MenuItem::subMenu('Users', 'fa fa-address-card')->setSubItems([
           /*  MenuItem::linkToCrud('Absence', 'fa fa-user-times', Absence::class),
            MenuItem::linkToCrud('Autorisation Sortie', 'fa fa-hourglass-half', AutorisationSortie::class),
            MenuItem::linkToCrud('Conger', 'fa fa-calendar-minus-o', Conger::class),
            MenuItem::linkToCrud('Pointage', 'fa fa-hand-pointer-o', Pointage::class),
            MenuItem::linkToCrud('Dbf', 'fa fa-file', Dbf::class),
            MenuItem::linkToCrud('Xlsx', 'fa fa-file-excel-o', Xlsx::class), */
            MenuItem::linkToCrud('Employer', 'fa fa-user', User::class),
        ]);/*
        yield MenuItem::subMenu('Config', 'fa fa-cogs')->setSubItems([
            MenuItem::linkToCrud('Config', 'fa fa-cog', Config::class),
            MenuItem::linkToCrud('Jour Ferier', 'fa fa-calendar-times-o', JourFerier::class),
            MenuItem::linkToCrud('Horaire', 'fa fa-calendar', Horaire::class),
            MenuItem::linkToCrud('WorkTime', 'fa fa-id-card', WorkTime::class),
        ]); */
    }
}
