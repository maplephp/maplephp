<?php
/**
 * Shell router
 *
 * @var RouterDispatcher $router
 */

use MaplePHP\Core\Router\RouterDispatcher;
use App\Controllers\TestController;
use App\Controllers\HelpController;


// Bind Middleware to router with `with(TestMiddleware::class)`
// use MaplePHP\Unitary\Console\Middlewares\TestMiddleware;
// $router->map(["", "test", "run"], [RunTestController::class, "run"])->with(TestMiddleware::class)

$router->cli("[/help]", [HelpController::class, "index"]);
$router->cli("/run", [TestController::class, "index"]);
