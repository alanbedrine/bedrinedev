<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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
            'category/index.html.twig',
            [
                'categorys' => $categorys
            ]
        );
    }

    #[Route('/categorie/{slug}', name: 'app_category_detail')]
    public function detail(
        string $slug,
        Category $category = null,
        CategoryRepository $repo
    ): Response {
        $category = $repo->findOneBy(['slug' => $slug]);
        if (!$category) {
            return $this->redirectToRoute('app_category');
        }

        $categoryId = $category->getId();

        $products = $repoProduct->find(['category' => $categoryId]);

        return $this->render(
            'category/detail.html.twig',
            [
                'category' => $category
            ]
        );
    }
}
