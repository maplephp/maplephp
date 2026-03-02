<?php

namespace App\Controllers;



use MaplePHP\Core\Routing\DefaultController;
use Psr\Http\Message\ResponseInterface;

class StartController extends DefaultController
{

    public function index(ResponseInterface $response): ResponseInterface
    {

        $response->getBody()->write("Hello World!");
        return $response;
    }

}
