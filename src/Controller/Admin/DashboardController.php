<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\User;
use App\Entity\Advert;
use App\Entity\Address;
use App\Entity\Article;
use App\Entity\Product;
use App\Entity\Setting;
use App\Entity\Sliders;
use App\Entity\Category;
use App\Entity\Application;
use App\Entity\Collections;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ProductCrudController;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     // Redirect non-admin users to a different route, such as the homepage
        //     return new RedirectResponse($this->generateUrl('app_home'));
        // }

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('E Shop');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Products', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-tag', Category::class);
        yield MenuItem::linkToCrud('Sliders', 'fas fa-image', Sliders::class);
        yield MenuItem::linkToCrud('Collections', 'fas fa-panorama', Collections::class);
        yield MenuItem::linkToCrud('Pages', 'fa-solid fa-pager', Page::class);
        yield MenuItem::linkToCrud('Settings', 'fa-solid fa-gear', Setting::class);
    }
}