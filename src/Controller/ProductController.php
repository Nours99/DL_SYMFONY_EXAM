<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/addProduct", name="add_product")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $AddProductForm = $this->createForm(AddProductFormType::class, $product);
        $AddProductForm->handleRequest($request);

        $user = $this->getUser();

        if ($AddProductForm->isSubmitted() && $AddProductForm->isValid()) {
            $product->setName($AddProductForm->get('name')->getData());
            $product->setDescription($AddProductForm->get('description')->getData());
            $product->setPrice($AddProductForm->get('price')->getData());
            $product->setAddedAt(new \DateTime());

            $image = $AddProductForm->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename);
                } catch (FileException $exception) {
                    throwException($exception);
                }

                $product->setImage($newFilename);
            }

            $status = $AddProductForm->get('status')->getData();

            if ($status) {
                $product->setStatus(false);
            } else {
                $product->setStatus(true);
            }

            $product->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('product/add.html.twig', [
            'AddProductForm' => $AddProductForm->createView()
        ]);
    }

    /**
     * @Route("/modifyProduct/{id}", name="modify_product")
     */
    public function update(String $id): Response
    {
        $productId = intval($id);
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if($product->getStatus()){
            $product->setStatus(false);
        } else if (!$product->getStatus()) {
            $product->setStatus(true);
        }
        $entityManager->flush();
        return $this->redirectToRoute('dashboard');
    }
}
