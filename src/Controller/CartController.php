<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var CartService
     */
    private $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    /**
     * @Route ("/cart/add/{id}", name="cart_add", requirements={"id": "\d+"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function add($id, Request $request)
    {
        $product = $this->productRepository->find($id);

        if(!$product)
        {
            throw $this->createNotFoundException('Le produit demander n\'excite pas');
        }

        $this->cartService->add($id);

        $this->addFlash('success', 'Le produit à bien été ajouté au panier');

        if($request->query->get('returnToCart'))
        {
            return $this->redirectToRoute('cart_show');
        }
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     * @return Response
     */
    public function show()
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getDetailedCartItems(),
            'total' => $this->cartService->getTotal()
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     * @param $id
     * @return RedirectResponse
     */
    public function delete($id)
    {
        $product = $this->productRepository->find($id);

        if(!$product)
        {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        $this->cartService->remove($id);

        $this->addFlash("success", "Le produit à bien été supprimé du panier");

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id": "\d+"})
     * @param $id
     * @return RedirectResponse
     */
    public function decrement($id)
    {
        $product = $this->productRepository->find($id);

        if(!$product)
        {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        $this->cartService->decrement($id);

        $this->addFlash('success', 'Le produit à bien été décrémenté');

        return $this->redirectToRoute('cart_show');
    }
}
