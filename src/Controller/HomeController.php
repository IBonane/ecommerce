<?php

namespace App\Controller;

use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        SettingRepository $settingRepo, 
        // SlidersRepository $slidersRepo 
    ): Response
    {
        $session = $request->getSession();
        $data = $settingRepo->findAll();
        // $sliders = $slidersRepo->findAll();

        
        $session->set("setting", $data[0]);

        return $this->render('home/index.html.twig', [
            // 'sliders' => $sliders
        ]);
    }
}