<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrowseController extends AbstractController
{
    /**
     * @Route("/browse", name="browse")
     */
    public function index(): Response
    {
        $productsRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productsRepository->findBy(['status' => true]);
        return $this->render('browse/browse.html.twig', [
            'products' => $products
        ]);
    }
}
