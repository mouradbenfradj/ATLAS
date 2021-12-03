<?php

namespace App\Controller\Admin;

use App\Entity\Absence;
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
use App\Entity\Xlsx;
use App\Repository\UserRepository;
use App\Service\BilanService;
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
    private $bilanService;
    private $userRepository;

    public function __construct(AdminContextProvider $adminContextProvider, BilanService $bilanService, UserRepository $userRepository)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->userRepository = $userRepository;
        $this->bilanService = $bilanService;
    }
    /**
     * @Route("/{_locale}/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        if (!empty($this->adminContextProvider->getContext()->getRequest()->request->all())) {
            $employer = $this->getDoctrine()->getRepository(User::class)->find($this->adminContextProvider->getContext()->getRequest()->request->get('user'));
        } else {
            $employer = $this->getUser();
        }
        //usort($user->getPointages(), fn ($a, $b) => $a['date'] > $b['date'])
        if ($employer and property_exists($employer, 'pointages')) {
            $bilans = $this->bilanService->getBilanGeneral($employer->getPointages()->toArray());
        } else {
            $bilans= [];
        }
        return $this->render('admin/dashboard.html.twig', [
            //'users' => $this->getDoctrine()->getRepository(User::class)->findAll(),
            'users' => $this->userRepository->findAll(),
            'userBilan' =>  $employer,
            'bilan' => $bilans
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ATLAS')
            ->generateRelativeUrls()
            ->disableUrlSignatures()
            ->setFaviconPath('favicon.svg')
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fa fa-id-card', EntityClass::class);
        yield MenuItem::subMenu('bilan', 'fa fa-gavel')->setSubItems([
            MenuItem::linkToRoute('Bilan annuel', 'fa fa-thermometer-full', 'default'),
            MenuItem::linkToRoute('Bilan mensuel', 'fa fa-thermometer-half', 'default'),
            MenuItem::linkToRoute('Bilan semestiriel', 'fa fa-thermometer-quarter', 'default'),
        ]);
        yield MenuItem::subMenu('Users', 'fa fa-address-card')->setSubItems([
            MenuItem::linkToCrud('Absence', 'fa fa-user-times', Absence::class),
            MenuItem::linkToCrud('Autorisation Sortie', 'fa fa-hourglass-half', AutorisationSortie::class),
            MenuItem::linkToCrud('Conger', 'fa fa-calendar-minus-o', Conger::class),
            MenuItem::linkToCrud('Pointage', 'fa fa-hand-pointer-o', Pointage::class),
            MenuItem::linkToCrud('Dbf', 'fa fa-file', Dbf::class),
            MenuItem::linkToCrud('Xlsx', 'fa fa-file-excel-o', Xlsx::class),
            MenuItem::linkToCrud('Employer', 'fa fa-user', User::class),
        ]);
        yield MenuItem::subMenu('Config', 'fa fa-cogs')->setSubItems([
            MenuItem::linkToCrud('Config', 'fa fa-cog', Config::class),
            MenuItem::linkToCrud('Jour Ferier', 'fa fa-calendar-times-o', JourFerier::class),
            MenuItem::linkToCrud('Horaire', 'fa fa-calendar', Horaire::class),
            MenuItem::linkToCrud('WorkTime', 'fa fa-id-card', WorkTime::class),
        ]);
    }


    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getUsername())
            //->setName($user->getFullName())
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
