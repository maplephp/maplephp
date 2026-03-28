<?php

use MaplePHP\Core\Interfaces\ErrorPageInterface;
use MaplePHP\Core\Render\Errors\SimpleErrorPage;
use MaplePHP\Core\Render\Errors\TestPage;

return [
	"bindings" => [
		ErrorPageInterface::class => SimpleErrorPage::class,
	],
	"providers" => [
		\MaplePHP\Core\Providers\DatabaseProvider::class,
		\MaplePHP\Core\Providers\TwigServiceProvider::class,
	]
];
