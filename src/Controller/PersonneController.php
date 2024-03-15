<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\ErrorHandler\Collecting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne/add', name: 'personne')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
       // $this->getDoctrine(); symfony <=5
        $entityManger = $doctrine->getManager();
        $personne = new Personne();
        $personne->setFirstName('Asma');
        $personne->setLastName('Abichou');
        $personne->setAge('25');

        // insert personne
        $entityManger->persist($personne);
        $entityManger->flush();
        //execute transaction
        return $this->render('personne/details.html.twig', [
            'personne' => $personne,
        ]);
    }
}
