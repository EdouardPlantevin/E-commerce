<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;

class HelloController
{

    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
    /**
    * @Route("/hello/{name?World}", name="hello")
    */

    public function hello($name, Slugify $slugify)
    {
        $test = $slugify->slugify(("Hello World!!"));
        $prix = $this->calculator->calcul(100);
        return new Response("Hello $name + voici le prix: $prix, voici le slug: $test");
    }
}