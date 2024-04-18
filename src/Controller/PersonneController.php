<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use App\Service\Helpers;
use App\Service\PdfService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('personne'), IsGranted('ROLE_USER')]
class PersonneController extends AbstractController
{
    public function __construct( private LoggerInterface $logger,
                                 private Helpers $helper)
    {
    }
    #[Route('/', name: 'personne.list', methods: 'GET')]
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
    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf) {
        $html = $this->render('personne/details.html.twig',
            [
                'personne' => $personne
            ]);
        $pdf->showPdfFile($html);
    }
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.stats.age')]
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
    #[Route('/search/{page?1}/{nbre?24}', name: 'personne.search', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function search(PersonneRepository $personneRepository, Request $request, $page, $nbre = 24): Response
    {
        $querySearch = trim($request->query->get('searchQuery', ''));
        if ($querySearch === '') {
            return $this->redirectToRoute('personne.list.all');
        }
        //dd($querySearch);
        $personnes = $personneRepository->searchByNameWithPagination($querySearch, $nbre, ($page - 1) * $nbre);
        if (empty($personnes)) {
            $this->addFlash('error', 'No results found for the search.');
        }
        //dd($querySearch);
        $totalCount = $personneRepository->countByName($querySearch);
        //dd($totalCount);
        $nbrePage = ceil($totalCount / $nbre);
            return $this->render('personne/index.html.twig', [
                'personnes' => $personnes,
                'isPaginated' => true,
                'nbrePage' => $nbrePage,
                'page' => $page,
                'nbre' => $nbre,
                'totalSearchResult' => $totalCount,
                'querySearch'=>$querySearch,
            ]);
        }
    #[Route("/ajax/search", name: "search_persons_via_ajax", methods: "GET")]
    public function searchPersonsViaAjax(Request $request, PersonneRepository $personneRepository): Response
    {
        $searchQuery = $request->query->get('query');
        $persons = $personneRepository->searchByName($searchQuery);
        return $this->json($persons, 200, [], ["groups" => "show_person"]);
        //dd($persons);
    }


    #[Route('/all/{page?1}/{nbre?12}', name: 'personne.list.all'), IsGranted('ROLE_USER')]
    public function indexall(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrePage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, ($page - 1) * $nbre);
        return $this->render('personne/list.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'nbre' => $nbre,

        ]);
    }

  /*  #[Route('/list', name: 'people_list')]
    public function list(PersonneRepository $personneRepository): Response
    {
        $allPersons = $personneRepository->findAll();
        $numberOfPersonsPerPage = 12;
        $numberOfPages = (int)ceil(count($allPersons) / $numberOfPersonsPerPage);
        //dd($numberOfPages);

        return $this->render('personne/index.html.twig', [
            'personnes' => $allPersons,
            'isPaginated' => true,
            'nbrePage' => $numberOfPages,
            'page' => $page,
            'nbre' => $nbre
        ]);
    }*/

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
    {
        if (!$personne) {
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/details.html.twig', ['personne' => $personne]);
    }

    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(Personne $personne = null, ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new = false;
        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            if($new) {
                $message = " a été ajouté avec succès";
            } else {
                $message = " a été mis à jour avec succès";
            }
            $manager = $doctrine->getManager();
            $manager->persist($personne);

            $manager->flush();
            $this->addFlash('success', $personne->getLastName() . ' a été mis à jour avec succès');
            $this->addFlash('success',$personne->getFirstName(). $message );
           return $this->redirectToRoute('personne.list');
        }
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView()
            ]);
    }
    #[Route('/delete/{id}', name: 'personne.delete'), IsGranted('ROLE_ADMIN')]
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