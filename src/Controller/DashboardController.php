<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(PersonneRepository $personneRepository): Response
    {
        $personne = $personneRepository->findAll();
       // dd($personne);
        return $this->render('dashboard/table.html.twig', [
            'personne' => $personne
        ]);
    }
}
