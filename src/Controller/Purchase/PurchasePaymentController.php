<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showCartForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {

        $purchase = $purchaseRepository->find($id);

        if(
            !$purchase ||
            ($this->getUser() !== $purchase->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID))
        {
            return $this->redirectToRoute('cart_show');
        }

        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'id' => $id,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
}