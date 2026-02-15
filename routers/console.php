<?php

use App\Controllers\TestController;
use App\Controllers\HelpController;

// Bind Middleware to router with `with(TestMiddleware::class)`
// use MaplePHP\Unitary\Console\Middlewares\TestMiddleware;
// $router->map(["", "test", "run"], [RunTestController::class, "run"])->with(TestMiddleware::class)

return $router
    ->map(["", "run"], [TestController::class, "index"])
    ->map(["__404", "help"], [HelpController::class, "index"]);
