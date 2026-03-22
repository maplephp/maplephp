<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use MaplePHP\Core\Routing\DefaultController;
use MaplePHP\Http\Interfaces\PathInterface;


class HelloWorldController extends DefaultController
{
	/**
	 * Default route example.
	 *
	 * The response body is written to using the PSR-7 stream and the
	 * modified response is returned to the framework.
	 *
	 * @param ResponseInterface $response Response instance used to write output
	 * @param PathInterface $path Provides access to route parameters and segments
	 * @return ResponseInterface
	 */
    public function index(ResponseInterface $response, PathInterface $path): ResponseInterface
    {
        $response->getBody()->write("Hello World!<br>");
	    $response->getBody()->write("<a href='" . $path->uri()->withPath("/show") . "'>Show</a>");
        return $response;
    }

	/**
	 * Example route demonstrating how to read values from the path.
	 *
	 * The response body is written to using the PSR-7 stream and the
	 * modified response is returned to the framework.
	 *
	 * @param ResponseInterface $response Response instance used to write output
	 * @param PathInterface $path Provides access to route parameters and segments
	 * @return ResponseInterface
	 */
	public function show(ResponseInterface $response, PathInterface $path): ResponseInterface
	{
		$response->getBody()->write("Hello World 2!<br>");
		$response->getBody()->write("Hello form: " . $path->select("page")->last());
		return $response;
	}
}
