<?php

use MaplePHP\Core\Middlewares\HttpStatusError;
use MaplePHP\Emitron\Middlewares\ContentLengthMiddleware;
use MaplePHP\Core\Render\Errors\TwigErrorPage;
use MaplePHP\Emitron\Middlewares\GzipMiddleware;

return [
	"middleware" => [
		"global" => [
			new HttpStatusError(new TwigErrorPage()),
			ContentLengthMiddleware::class,
			//GzipMiddleware::class
		]
	]
];