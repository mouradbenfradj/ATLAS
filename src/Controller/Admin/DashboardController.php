<?php

namespace App\Controller\Admin;

use App\Entity\Horaire;
use App\Entity\JourFerier;
use App\Entity\Pointage;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin_dashboard")
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
        yield MenuItem::subMenu('bilan', 'fa fa-article')->setSubItems([
            MenuItem::linkToRoute('Bilan annuel', 'fas fa-list', 'default_index'),
            MenuItem::linkToRoute('Bilan mensuel', 'fas fa-list', 'default_index'),
            MenuItem::linkToRoute('Bilan semestiriel', 'fas fa-list', 'default_index'),
        ]);
        yield MenuItem::subMenu('Users', 'fa fa-article')->setSubItems([
            MenuItem::linkToCrud('Pointage', 'fas fa-list', Pointage::class),
            MenuItem::linkToCrud('Employer', 'fas fa-list', User::class),
        ]);
        yield MenuItem::subMenu('Config', 'fa fa-article')->setSubItems([
            MenuItem::linkToCrud('Jour Ferier', 'fas fa-list', JourFerier::class),
            MenuItem::linkToCrud('Horaire', 'fas fa-list', Horaire::class),
        ]);
    }


    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getFirstName())
            // use this method if you don't want to display the name of the user
            ->displayUserName(false)

            // you can return an URL with the avatar image
            //->setAvatarUrl('https://...')
            //->setAvatarUrl($user->getProfileImageUrl())
            // use this method if you don't want to display the user image
            //->displayUserAvatar(false)
            // you can also pass an email address to use gravatar's service
            //->setGravatarEmail($user->getMainEmailAddress())

            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }
}
