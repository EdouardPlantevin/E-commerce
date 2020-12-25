<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class TestController
{
    public function index()
    {
        dd("Hello World");
    }

    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"})
     */

    public function test(Request $request, $age)
    {
        $name = $request->query->get('prenom', 'Inconnu');

        return new Response("Vous avez $age ans! et vous vous appeler $name");
    }
}