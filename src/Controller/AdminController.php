<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin/products', name: 'admin_products')]
    public function products(): Response
    {
        return $this->render('admin/products.html.twig', [
            'crud_name' => 'Productos',
            'create_route' => $this->generateUrl('admin_products')
        ]);
    }

    #[Route('/admin/categories', name: 'admin_categories')]
    public function categories(): Response
    {
        return $this->render('admin/categories.html.twig', [
            'crud_name' => 'Categorías',
            'create_route' => $this->generateUrl('admin_categories')
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/users.html.twig', [
            'crud_name' => 'Usuarios',
            'create_route' => $this->generateUrl('admin_users')
        ]);
    }
}
