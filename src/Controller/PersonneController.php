<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\ErrorHandler\Collecting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    }
    #[Route('/all/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function byAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes
        ]);
    }

    #[Route('/all/{page?1}/{nbre?12}', name: 'personne.list.all')]
    public function indexall(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrePage = ceil($nbPersonne / $nbre);
        //dd(ceil($nbPersonne / $nbre));
        $personnes = $repository->findBy([], [], $nbre, ($page - 1) * $nbre);
        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
        if (!$personne) {
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }

        return $this->render('personne/details.html.twig', ['personne' => $personne]);
    }

    #[Route('/add', name: 'personne.add')]
    public function add(ManagerRegistry $doctrine): Response
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

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function delete(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
       //get person to delete
        // if exist delete and return flash success massage
            if($personne){
                $manager = $doctrine->getManager();
                $manager->remove($personne);
                $manager->flush();
                $this->addFlash('success',"la personne a été supprimer avec succés");
            }else{
                //if not return flash error massage
                $this->addFlash('error',"la personne n'existe pas");
            }
           return $this->redirectToRoute('personne.list.all');
    }
    #[Route('/update/{id}/{lastName}/{firstName}/{age}', name: 'personne.update', methods: ['GET','POST'])]
    public function update(Personne $personne = null, $firstName, $lastName, $age, ManagerRegistry $doctrine): Response
    {
       //verify person to update existe
         //if person exist update return flash success message
        if($personne){
            $personne->setFirstName($firstName);
            $personne->setLastName($lastName);
            $personne->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', "La personne a été modifier avec succés");
        }else {
            $this->addFlash('error' , "La personne n'exist pas ");
            //if not return error flash message
        }
        return $this->redirectToRoute('personne.list.all');
    }

}