<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\StockHistoric;
use App\Form\AddRemoveStockType;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class StockHistoricController extends AbstractController
{
    #[Route('/stock-historic/{product}', name: 'admin_stock-historic')]
    public function index(Products $product): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $stockHistorics = $entityManager->getRepository(StockHistoric::class)->findBy(['product' => $product->getId()]);

        return $this->render('stock_historic/index.html.twig', [
            'crud_name' => 'Stock historic from '.$product->getName(),
            'back_route' => $this->generateUrl('admin_products'),
            'stockHistorics' => $stockHistorics
        ]);
    }

    #[Route('/add-remove-stock/{product}', name: 'admin_stock-historic_add-remove')]
    public function addRemoveStock(Request $request, Products $product): Response
    {
        $form = $this->createForm(AddRemoveStockType::class, $product);
        $form->handleRequest($request);
        $stockHistoric = new StockHistoric();
        $entityManager = $this->getDoctrine()->getManager();

        // Populate new historic
        $stockHistoric->setProduct($product);
        $stockHistoric->setUserId($this->get('security.token_storage')->getToken()->getUser());
        $stockHistoric->setCreatedAt(new DateTimeImmutable());

        // Get old product data
        $oldProduct = $entityManager->getUnitOfWork()->getOriginalEntityData($product);

        // If we are creating user
        if ($form->isSubmitted() && $form->isValid()) {
            if ($oldProduct['stock'] + $form->get('stock')->getData() >= 0) {
                $product->setStock($oldProduct['stock'] + $form->get('stock')->getData());
                $stockHistoric->setStock($product->getStock());
            } else {
                $this->addFlash('error', 'Actual stock cannot be negative');
                return $this->redirectToRoute('admin_stock-historic_add-remove', ['product' => $product->getId()]);
            }

            // Save entity
            $entityManager->persist($stockHistoric);
            $entityManager->flush();

            $this->addFlash('success', 'Stock updated successfully');
            return $this->redirectToRoute('admin_stock-historic', ['product' =>  $product->getId()]);
        }

        return $this->render('products/add_remove-stock-form.html.twig', [
            'crud_name' => 'Add/remove '.$product->getName().' stock',
            'addRemoveForm' => $form->createView(),
            'product' => $product
        ]);
    }
}
