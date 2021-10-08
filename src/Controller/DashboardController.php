<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $productsRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productsRepository->findBy(['user' => $user]);
        return $this->render('dashboard/dashboard.html.twig', [
            'products' => $products,
        ]);
    }
}
