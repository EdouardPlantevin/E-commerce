<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    private $cartService;
    private $entityManager;
    private $security;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager, Security $security)
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }


    public function storePurchase(Purchase $purchase)
    {
        $purchase->setUser($this->security->getUser())
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
    }
}