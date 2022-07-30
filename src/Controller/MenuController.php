<?php

namespace App\Controller;

use App\Repository\PlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategorieRepository;


class MenuController extends AbstractController
{
    #[Route('/Menu', name: 'Menu')]
    public function index(PlatRepository $platRepository,CategorieRepository $categorieRepository): Response
    {
        return $this->render('Pages/Menu.html.twig', [
            'plats' => $platRepository->findAll(),
            'statut' => '1',
            "titre" => "Our Menu",
            "categories"=>$categorieRepository->findAll()
        ]);
    }

    #[Route('/Corbeille',name: 'corbeille')]
    public function corbeil(PlatRepository $platRepository)
    {
        return $this->render('Pages/Menu.html.twig', [
            'plats' => $platRepository->findAll(),
            'statut' => '0',
            "titre" => "Corbeille"
        ]);
    }
}
