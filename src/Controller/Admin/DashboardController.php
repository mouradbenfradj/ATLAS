<?php

namespace App\Controller\Admin;

use App\Entity\Horaire;
use App\Entity\JourFerier;
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
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ATLAS');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::section('File');

        yield MenuItem::section('Users');
        yield MenuItem::section('Config');
        yield MenuItem::linkToCrud('Jour Ferier', 'fas fa-list', JourFerier::class);
        yield MenuItem::linkToCrud('Horaire', 'fas fa-list', Horaire::class);
    }
}
