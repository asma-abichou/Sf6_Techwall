<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Personne;
use App\Repository\JobRepository;
use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(PersonneRepository $personneRepository ): Response
    {
        $personnes = $personneRepository->findAll();
        return $this->render('dashboard/table.html.twig', [
            'personnes' => $personnes,
        ]);
    }
}
