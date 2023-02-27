<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CaisseOperationController extends AbstractController
{
    #[Route('/caisse/operation', name: 'app_caisse_operation')]
    public function index(): Response
    {
        return $this->render('caisse_operation/index.html.twig', [
            'controller_name' => 'CaisseOperationController',
        ]);
    }
}
