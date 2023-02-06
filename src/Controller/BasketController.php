<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/boutique')]
class BasketController extends AbstractController
{
    #[Route('/panier', name: 'app_basket')]
    public function index(
        Product $product = null,
        ProductRepository $repoProduct,
        Basket $basket = null,
        BasketRepository $repo,
        RequestStack $requestStack
    ): Response {
        $session = $requestStack->getSession();
        if (!$session->has('basket')) {
            $sessionId = rand();
            $session->set('basket', $sessionId);
        }
        $userId = $session->get('basket');


        $products = $repoProduct->find($id);

        return $this->render(
            'shop/basket.html.twig',
            [
                'products' => $products
            ]
        );
    }

    #[Route('/panier/add/{product}', name: 'app_basket_add', requirements: ['product' => '\d+'])]
    public function add(
        Product $product = null,
        ProductRepository $repoProduct,
        Basket $basket = null,
        BasketRepository $repo,
        RequestStack $requestStack
    ): Response {
        $product = $repoProduct->find($product);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $productId = $product->getId();

        $session = $requestStack->getSession();
        if (!$session->has('basket')) {
            $sessionId = rand();
            $session->set('basket', $sessionId);
        }
        $userId = $session->get('basket');

        $basket = new Basket();
        $basket->setUser($userId);
        $basket->setProduct($productId);

        $repo->save($basket, true);

        return $this->redirectToRoute('app_basket');
    }

    #[Route('/panier/deleted/{product}', name: 'app_basket_deleted', requirements: ['product' => '\d+'])]
    public function deleted(
        Product $product = null,
        ProductRepository $repoProduct,
        Basket $basket = null,
        BasketRepository $repo,
        RequestStack $requestStack
    ): Response {
        $productId = $product->getId();

        $session = $requestStack->getSession();
        if (!$session->has('basket')) {
            $sessionId = rand();
            $session->set('basket', $sessionId);
        }
        $userId = $session->get('basket');

        $basket = new Basket();
        $basket->setUser($userId);
        $basket->setProduct($productId);

        $repo->remove($basket, true);

        return $this->redirectToRoute('app_basket');
    }
}
