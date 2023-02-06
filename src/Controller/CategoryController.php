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
class CategoryController extends AbstractController
{
    #[Route('/categorie', name: 'app_category')]
    public function index(CategoryRepository $repository): Response
    {
        $categorys = $repository->findAll();

        return $this->render(
            'shop/index.html.twig',
            [
                'categorys' => $categorys
            ]
        );
    }

    #[Route('/categorie/{slug}', name: 'app_category_detail')]
    public function detail(
        string $slug,
        Category $category = null,
        CategoryRepository $repo,
        ProductRepository $repoProduct
    ): Response {
        $category = $repo->findOneBy(['slug' => $slug]);
        if (!$category) {
            return $this->redirectToRoute('app_category');
        }

        $categoryId = $category->getId();

        $products = $repoProduct->findBy(['category' => $categoryId]);

        return $this->render(
            'shop/category.html.twig',
            [
                'category' => $category,
                'products' => $products
            ]
        );
    }
}
