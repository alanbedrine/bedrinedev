<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/boutique')]
class ProductController extends AbstractController
{
    #[Route('/produits/{slug}', name: 'app_product_detail')]
    public function detail(
        string $slug,
        Product $product = null,
        ProductRepository $repo
    ): Response {
        $product = $repo->findOneBy(['slug' => $slug]);
        if (!$product) {
            return $this->redirectToRoute('app_category');
        }

        return $this->render(
            'shop/product.html.twig',
            [
                'product' => $product
            ]
        );
    }
}
