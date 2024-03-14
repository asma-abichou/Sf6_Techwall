<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends  AbstractController
{
    #[Route('tab/{nb<\d+>?5}', name: 'tab')]
    public function showTab($nb)
    {
        $notes = [];
        for($i = 0; $i<$nb; $i++){
            $notes[] = rand(0,10);
        }
        return $this->render('tab/tab.html.twig', [
            'notes' => $notes,
        ]);
    }
    #[Route('tab/users', name: 'tab.users')]
    public function users(): Response
    {
        $users = [
            ['firstName'=> 'asma' , 'lastName'=>'Abichou' , 'age'=> 24],
            ['firstName'=> 'imen' , 'lastName'=>'Abichou' , 'age'=> 34],
            ['firstName'=> 'olfa' , 'lastName'=>'Abichou' , 'age'=> 36],
            ['firstName'=> 'amira' ,'lastName'=>'Abichou' , 'age'=> 35],

        ];
        return $this->render('tab/users.html.twig', [
            'users' => $users
        ]);
    }
}