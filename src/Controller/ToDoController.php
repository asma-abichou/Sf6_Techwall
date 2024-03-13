<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use function mysql_xdevapi\getSession;

class ToDoController extends AbstractController
{
    #[Route('/todo', name: 'todo.show')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if(!$session->has('todos')){
            $todos = [
                'achat' => 'acheter clés USB',
                'cours' => 'finaliser mon cours',
                'coorection' =>'corriger mes examens',

            ];
            $session->set('todos', $todos);
            $this->addFlash('info', "La list des todos vien d'etre initialisée");
        }
        return $this->render('todo/index.html.twig', [
            'controller_name' => 'ToDoController',
        ]);
    }
    #[Route('/todo/add/{name}/{content}', name: 'todo.add', defaults: ['content'=>'sf6'])]
    public function addToDo(Request $request, $name, $content): RedirectResponse
    {

        $session = $request->getSession();
        //verify  to do if exist in session
        if(!$session->has('todos')){
            //if not
            //initialize the todos session variable
            $session->set('todos', []);
        }

        // verify is already exist to do list with the same name
        $todos = $session->get('todos');
        if(isset($todos[$name])){
            $this->addFlash('error', "Le todos d'id $name existe déja");
        } else {
            //if yes error
            //  not add and display a success message
            $todos[$name] = $content;
            $this->addFlash('success', "Le todo d'id $name a été ajouté avec succés");
            $session->set('todos', $todos);
        }

        return $this->redirectToRoute('todo.show');
    }
    //update todo list
    #[Route('/todo/update/{name}/{content}', name:'todo.update')]
    public function updateToDo(Request $request, $name, $content): RedirectResponse
    {
        $session = $request->getSession();
        if($session->has('todos')){
            $todos = $session->get('todos');
            if(!isset($todos[$name])){
                $this->addFlash('error', "Le todo d'id $name n'existe pas");
            }else{
                $todos[$name] = $content;
                $this->addFlash('success', "Le todo d'id $name a été modifié avec succés");
            }
            $session->set('todos', $todos);
        }else{
            $this->addFlash('error', " La todo list n'est pas encore initialisée ");
        }
        return $this->redirectToRoute('todo.show');
    }

    #[Route('/todo/delete/{name}', name:'todo.delete')]
    public function deleteToDo(Request $request, $name):RedirectResponse
    {
        $session = $request->getSession();
        if($session->has('todos')){
            //verify if to do exist in the list
            $todos = $session->get('todos');
            if(!isset($todos[$name])){
                $this->addFlash('error', "Le todo d'id $name n'existe pas");
            }else {
                unset($todos[$name]);
                $this->addFlash('success', "Le todo d'id $name a été supprimé avec succés");
            }
            $session->set('todos', $todos);
        }else {
            //if not
            //display error and redirect to route to do show
            $this->addFlash('error', "Le todo list n'est pas encore initialisée");
        }
        return $this->redirectToRoute('todo.show');

    }
    #[Route('/todo/reset', name: 'todo.reset')]
    public function resetToDo(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $session->remove('todos');
        $this->addFlash('success', "La liste des todos a été réinitialisée avec succès");
        return $this->redirectToRoute('todo.show');
    }

    #[Route('/multi/{entier1<\d+>}/{entier2<\d+>}')]
    public function multiplication($entier1 , $entier2){
        $result = $entier1*$entier2;
        return new Response("<h1>$result</h1>");
    }
}
