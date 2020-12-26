<?php 

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Calculator 
{
    protected $logger;
    protected $tva;

    public function __construct(LoggerInterface $loggerInterface, float $tva)
    {
        $this->logger = $loggerInterface;
        $this->tva = $tva;
    }

    public function calcul(float $prix) : float 
    {
        $this->logger->info("Un calcul à lieu: $prix");
        return $prix * ($this->tva / 100);
    }
}