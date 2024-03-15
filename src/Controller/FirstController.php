<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FirstController extends AbstractController
{
    #[Route('/template', name: 'template')]
    public function template(): Response
    {
        return $this->render('template.html.twig');
    }

    #[Route('/first', name: 'first')]
    public function index(): Response
    {
        return $this->render('first/index.html.twig',[
            'firstName' =>'Asma',
            'lastName' => 'Abichou'
        ]);
    }
   /* #[Route('/sayHello/{firstName}/{lastName}', name: 'say.hello')]*/
    public function sayHello(Request $request, $firstName , $lastName): Response
    {
        return $this->render('first/index.html.twig', [
            'firstName'=> $firstName,
            'lastName' => $lastName
        ]);
    }
}
