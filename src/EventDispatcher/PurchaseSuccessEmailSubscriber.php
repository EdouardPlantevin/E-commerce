<?php


namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $mailer;
    private $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }


    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $purchase = $purchaseSuccessEvent->getPurchase();

        $email = new TemplatedEmail();
        $email->to(new Address($user->getEmail()), $user->getUsername())
            ->from('contact@mail.com')
            ->subject('Merci de votre commande')
            ->htmlTemplate('email/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $user
            ])
        ;
        $this->mailer->send($email);

        $this->logger->info("Email envoyé pour la commande n° " . $purchaseSuccessEvent->getPurchase()->getId());
    }
}