<?php

namespace App\Controller;

use App\Repository\ProjetsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProjetsRepository $projetsRepository): Response
    {
        $projet =$projetsRepository->findAll();
        return $this->render('home/index.html.twig', [
            'projets'=>$projet,
           
        ]);
    }
}