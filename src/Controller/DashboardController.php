<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Personne;
use App\Repository\JobRepository;
use App\Repository\PersonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('dashboard'), IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(PersonneRepository $personneRepository ): Response
    {
        $personnes = $personneRepository->findAll();
        return $this->render('dashboard/table.html.twig', [
            'personnes' => $personnes,
        ]);
    }
    #[Route('/medecin', name: 'medecin.details')]
    public function getPersonDjob(PersonneRepository $personneRepository ): Response
    {
        $personnes = $personneRepository->findall();

      return $this->render('dashboard/table.html.twig', [
            'personnes' => $personnes,
        ]);
    }
}
