<?php

namespace App\Controller\Admin;

use App\Entity\Abscence;
use App\Entity\User;
use App\Entity\Conger;
use App\Entity\Horaire;
use App\Entity\Pointage;
use App\Entity\JourFerier;
use App\Service\PointageService;
use App\Entity\AutorisationSortie;
use App\Entity\Config;
use App\Entity\Dbf;
use App\Entity\WorkTime;
use App\Repository\UserRepository;
use App\Repository\PointageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    private $adminContextProvider;
    private $pointageService;

    public function __construct(AdminContextProvider $adminContextProvider, PointageService $pointageService)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->pointageService = $pointageService;
    }
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        if (!empty($this->adminContextProvider->getContext()->getRequest()->request->all()))
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->adminContextProvider->getContext()->getRequest()->request->get('user'));
        else
            $user = $this->getUser();
        //usort($user->getPointages(), fn ($a, $b) => $a['date'] > $b['date'])

        $collectGeneral = $this->pointageService->getBilanGeneral($user->getPointages()->toArray());
        return $this->render('admin/dashboard.html.twig', [
            'users' => $this->getDoctrine()->getRepository(User::class)->findAll(),
            'userBilan' =>  $user,
            'bilan' => $collectGeneral
        ]);
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
            MenuItem::linkToCrud('Abscence', 'fas fa-list', Abscence::class),
            MenuItem::linkToCrud('Autorisation Sortie', 'fas fa-list', AutorisationSortie::class),
            MenuItem::linkToCrud('Conger', 'fas fa-list', Conger::class),
            MenuItem::linkToCrud('Pointage', 'fas fa-list', Pointage::class),
            MenuItem::linkToCrud('Dbf', 'fas fa-list', Dbf::class),
            MenuItem::linkToCrud('Employer', 'fas fa-list', User::class),
        ]);
        yield MenuItem::subMenu('Config', 'fa fa-article')->setSubItems([
            MenuItem::linkToCrud('Config', 'fas fa-list', Config::class),
            MenuItem::linkToCrud('Jour Ferier', 'fas fa-list', JourFerier::class),
            MenuItem::linkToCrud('Horaire', 'fas fa-list', Horaire::class),
            MenuItem::linkToCrud('WorkTime', 'fas fa-list', WorkTime::class),
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
