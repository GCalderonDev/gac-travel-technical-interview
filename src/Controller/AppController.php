<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'stock_history')]
    public function products(): Response
    {
        return $this->render('stock_historic.html.twig', [
            'crud_name' => 'Histórico de stock',
            'create_route' => $this->generateUrl('stock_history')
        ]);
    }
}
