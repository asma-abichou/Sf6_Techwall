<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\Collections\Collection;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    public function __construct( private LoggerInterface $logger,
                                 private Helpers $helper)
    {
    }
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
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function statsPersonneByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        //dd($stats);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin'=>$ageMin,
            'ageMax'=>$ageMax
        ]);
    }

    #[Route('/all/{page?1}/{nbre?12}', name: 'personne.list.all')]
    public function indexall(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $helper = new Helpers();
        echo $helper->sayCC();
       // dd($helpers->sayCC());
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

    #[Route('/personne/add', name: 'personne.add')]
    public function addPerson(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', 'Personne ajoutée avec succès');
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/add-personne.html.twig', [
            'form' => $form->createView()
        ]);
    }

// Separate route for edit person
    #[Route('/personne/edit/{id}', name: 'personne.edit')]
    public function editPerson(Personne $personne, ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', 'Personne modifiée avec succès');
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/add-personne.html.twig', [
            'form' => $form->createView()
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