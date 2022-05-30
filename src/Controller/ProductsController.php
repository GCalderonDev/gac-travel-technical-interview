<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'admin_products')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager->getRepository(Products::class)->findAll();

        return $this->render('products/index.html.twig', [
            'crud_name' => 'Products',
            'products' => $products,
            'create_route' => $this->generateUrl('admin_products_create')
        ]);
    }

    #[Route('/products/create', name: 'admin_products_create')]
    public function create(Request $request): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        // If we are creating a product
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setName($form->get('name')->getData());
            $product->setCategory($form->get('category')->getData());
            $product->setCreatedAt(new DateTimeImmutable());

            // Save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully');
            return $this->redirectToRoute('admin_products');
        }

        // Return form
        return $this->render('products/create-edit-form.html.twig', [
            'crud_name' => 'Create product',
            'productForm' => $form->createView()
        ]);
    }

    #[Route('/products/{product}/edit', name: 'admin_products_edit')]
    public function edit(Request $request, Products $product): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        // If we are updating a product
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setName($form->get('name')->getData());
            $product->setStock($form->get('stock')->getData());
            $product->setCategory($form->get('category')->getData());

            // Save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully');
            return $this->redirectToRoute('admin_products');
        }

        // Return form
        return $this->render('products/create-edit-form.html.twig', [
            'crud_name' => 'Edit product: '.$product->getName(),
            'product' => $product,
            'productForm' => $form->createView()
        ]);
    }

    #[Route('/products/delete/{product}', name: 'admin_products_delete')]
    public function delete(Products $product): Response
    {
        // Delete entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Product deleted successfully');
        return $this->redirectToRoute('admin_products');
    }
}
