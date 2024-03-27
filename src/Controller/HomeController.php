<?php

namespace App\Controller;

use App\Repository\PageRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use App\Repository\CategoryRepository;
use App\Repository\CollectionsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(private ProductRepository $productRepo){}

    #[Route('/', name: 'app_home')]
    public function index(
        SettingRepository $settingRepo, 
        SlidersRepository $slidersRepo, 
        CollectionsRepository $collectionRepo, 
        PageRepository $pageRepo,
        CategoryRepository $categoryRepo,
        Request $request
        ): Response
    {
        $session = $request->getSession();
        $data = $settingRepo->findAll();
        $sliders = $slidersRepo->findAll();
        $collections = $collectionRepo->findAll();

        $categories = $categoryRepo->findBy(['isMega' => true]);
        
        $session->set("setting", $data[0]);

        $headerPages = $pageRepo->findBy(['isHead'=> true]);
        $footerPages = $pageRepo->findBy(['isFoot'=> true]);

        $session->set("headerPages", $headerPages);
        $session->set("footerPages", $footerPages);
        $session->set("categories", $categories);

        return $this->render('home/index.html.twig', [
            'sliders' => $sliders,
            'collections' => $collections,
            'productsBestSeller' => $this->productRepo->findBy(['isBestSeller'=>true]),
            'productsNewArrival' => $this->productRepo->findBy(['isNewArrival'=>true]),
            'productsFeatured' => $this->productRepo->findBy(['isFeatured'=>true]),
            'productsSpecialOffer' => $this->productRepo->findBy(['isSpecialOffer'=>true])
        ]);
    }

    #[Route('/product/{slug}', name: 'app_show_product')]
    public function showProduct(string $slug): Response
    {
        $product = $this->productRepo->findOneBy(['slug' => $slug]);

        if(!$product){
            return $this->render('page/not-found.html.twig');
        }

        return $this->render('product/show_product_by_slug.html.twig', [
            'product' => $product
        ]);
    }
}