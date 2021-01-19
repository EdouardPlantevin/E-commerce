<?php


namespace App\EventDispatcher;


use App\Event\ProductShowEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductShowEmailSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public static function getSubscribedEvents()
    {
        return [
            'product.show' => 'sendShowEmail'
        ];
    }

    public function sendShowEmail(ProductShowEvent $productShowEvent)
    {
        $this->logger->info("Email envoyer, le produit: " . $productShowEvent->getProduct()->getName() . "à été consulter");
    }
}