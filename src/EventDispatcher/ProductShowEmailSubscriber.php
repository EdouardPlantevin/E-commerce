<?php


namespace App\EventDispatcher;

use App\Event\ProductShowEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ProductShowEmailSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }


    public static function getSubscribedEvents()
    {
        return [
            'product.show' => 'sendShowEmail'
        ];
    }

    public function sendShowEmail(ProductShowEvent $productShowEvent)
    {
       /* $email = new TemplatedEmail();
        $email->from(new Address('plantevin.contact@gmail.com', 'info-de-la-boutique'))
                ->to('plantevin.contact@gmail.com')
                ->text('Un visiteur est en train de voir la page du produits: ' . $productShowEvent->getProduct()->getName())
                ->htmlTemplate('email/product_show.html.twig')
                ->context(['product' => $productShowEvent->getProduct()])
                ->subject('Visite du produit n°' . $productShowEvent->getProduct()->getId());

        $this->mailer->send($email);*/
        $this->logger->info("Email envoyer, le produit: " . $productShowEvent->getProduct()->getName() . "à été consulter");
    }
}