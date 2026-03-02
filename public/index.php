<?php
/**
 * MaplePHP Unitary bin file
 */

use MaplePHP\Blunder\Handlers\HtmlHandler;
use MaplePHP\Core\HttpKernel;

$root = realpath(__DIR__ . "/../");
$autoload = $root . '/vendor/autoload.php';
$autoload = realpath($autoload);

if (!$autoload || !is_file($autoload)) {
    if (!empty($GLOBALS['_composer_autoload_path'])) {
        $autoload = $GLOBALS['_composer_autoload_path'];
    } else {
        die("Autoloader not found. Run `composer install`.\n");
    }
}

require $autoload;

$app = (new HttpKernel($root))
    ->withErrorHandler(new HtmlHandler())
    ->boot([
        "argv" => [],
        "dir" => __DIR__
    ]);
