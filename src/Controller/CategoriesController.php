<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'admin_categories')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('categories/index.html.twig', [
            'crud_name' => 'Categories',
            'categories' => $categories,
            'create_route' => $this->generateUrl('admin_categories_create')
        ]);
    }

    #[Route('/categories/create', name: 'admin_categories_create')]
    public function create(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        // If we are creating a category
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($form->get('name')->getData());
            $category->setCreatedAt(new DateTimeImmutable());

            // Save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('categories/create-edit-form.html.twig', [
            'crud_name' => 'Create category',
            'categoryForm' => $form->createView()
        ]);
    }

    #[Route('/categories/{category}/edit', name: 'admin_categories_edit')]
    public function edit(Request $request, Categories $category): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        // If we are updating a category
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($form->get('name')->getData());

            // Save entity
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('categories/create-edit-form.html.twig', [
            'crud_name' => 'Edit category: '.$category->getName(),
            'category' => $category,
            'categoryForm' => $form->createView()
        ]);
    }

    #[Route('/categories/delete/{category}', name: 'admin_categories_delete')]
    public function delete(Categories $category): Response
    {
        // Save entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Category deleted successfully');
        return $this->redirectToRoute('admin_categories');
    }
}
