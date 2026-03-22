<?php
/**
 * Web router
 *
 * @var RouterDispatcher $router
 *
 * FastRoute parameter patterns:
 *
 * {name}              Matches any segment except "/". (Example: /user/{name})
 *
 * {id:\d+}            Matches numeric values only. (Example: /post/{id})
 *
 * {name:[^/]+}        Explicit single path segment. (Example: /profile/{name:[^/]+})
 *
 * {slug:.+}           Matches everything including slashes. (Example: /cat/{slug:.+})
 *
 * {lang:(en|sv|de)}   Restricts parameter to specific values. (Example: /{lang}/docs)
 */

use App\Controllers\HelloWorldController;
use MaplePHP\Core\Router\RouterDispatcher;

$router->get("/", [HelloWorldController::class, "index"]);
$router->get("/{page}", [HelloWorldController::class, "show"]);
