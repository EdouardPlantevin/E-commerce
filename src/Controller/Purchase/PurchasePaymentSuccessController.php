<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{

    private $entityManager;
    private $cartService;

    public function __construct(EntityManagerInterface $entityManager, CartService $cartService)
    {

        $this->entityManager = $entityManager;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     * @param $id
     * @param PurchaseRepository $purchaseRepository
     */
    public function success($id, PurchaseRepository $purchaseRepository)
    {
        $purchase = $purchaseRepository->find($id);

        if(
            !$purchase ||
            ($this->getUser() !== $purchase->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID))
        {
            $this->addFlash("warning", "La commande n'existe pas");
            return $this->redirectToRoute('purchase_index');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);

        $this->cartService->empty();
        $this->entityManager->flush();


        $this->addFlash('success', 'La commande à été payée et confirmer');
        return $this->redirectToRoute('purchase_index');
    }
}