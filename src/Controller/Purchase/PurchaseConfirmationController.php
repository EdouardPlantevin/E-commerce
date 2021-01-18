<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseConfirmationController extends AbstractController
{
    private $cartService;
    private $entityManager;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecter pour confirmer une commande")
     * @param Request $request
     * @return RedirectResponse
     */
    public function confirm(Request $request)
    {
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        if(!$form->isSubmitted())
        {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }

        /** @var User $user */
        $user = $this->getUser();

        $cartItems = $this->cartService->getDetailedCartItems();

        if(count($cartItems) === 0)
        {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        /** @var Purchase $purchase */
        $purchase = $form->getData();

        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

        foreach ($this->cartService->getDetailedCartItems() as $cartItem)
        {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                        ->setProduct($cartItem->product)
                        ->setProductName($cartItem->product->getName())
                        ->setQuantity($cartItem->quantity)
                        ->setTotal($cartItem->getTotal())
                        ->setProductPrice($cartItem->product->getPrice());

            $this->entityManager->persist($purchaseItem);
        }
        $this->entityManager->persist($purchase);
        $this->entityManager->flush();

        $this->cartService->empty();

        $this->addFlash('success', 'La commande à bien été enregistrer');

        return $this->redirectToRoute('purchase_index');
    }
}