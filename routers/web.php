<?php
/**
 * Web router
 *
 * @var RouterDispatcher $router
 */
use MaplePHP\Core\Router\RouterDispatcher;


//$router->cli("/run", [TestController::class, "index"]);


$router->group(function($router) {

    $router->get("/", [\App\Controllers\StartController::class, "index"]);
}, []);


//$router->get("/run", [TestController::class, "index"]);
//$router->get("[/help]", [HelpController::class, "index"]);