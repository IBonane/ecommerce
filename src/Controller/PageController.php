<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    #[Route('/page/{slug}', name: 'app_page')]
    public function index(Request $request, String $slug, PageRepository $pageRepo): Response
    {
        $page = $pageRepo->findOneBy(['slug' => $slug]);

        if(!$page){
            return $this->render('page/not-found.html.twig');
        }

        return $this->render('page/index.html.twig', [
            'page' => $page,
        ]);
    }

    // #[Route('/page/notfound', name: 'app_page_not_found')]
    // public function notfound(): Response
    // {
    //     return $this->render('page/not-found.html.twig', [
    //         'page' => $page,
    //     ]);
    // }
}
