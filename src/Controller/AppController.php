<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'products')]
    public function products(): Response
    {
        return $this->render('products.html.twig', [
            'crud_name' => 'Productos',
            'create_route' => $this->generateUrl('products')
        ]);
    }

    #[Route('/categories', name: 'categories')]
    public function categories(): Response
    {
        return $this->render('categories.html.twig', [
            'crud_name' => 'Categorías',
            'create_route' => $this->generateUrl('categories')
        ]);
    }

    #[Route('/users', name: 'users')]
    public function users(): Response
    {
        return $this->render('users.html.twig', [
            'crud_name' => 'Usuarios',
            'create_route' => $this->generateUrl('users')
        ]);
    }
}
