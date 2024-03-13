<?php

namespace App\Controller;

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
}