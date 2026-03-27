<?php

declare(strict_types=1);

namespace App\Controllers;

use MaplePHP\Core\Support\Twig;
use MaplePHP\DTO\Format\Clock;
use Psr\Http\Message\ResponseInterface;
use MaplePHP\Core\Routing\DefaultController;
use MaplePHP\Http\Interfaces\PathInterface;
use Twig\Environment;


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
	    $response->getBody()->write("<a href='" . $path->uri()->withPath("/hello/World") . "'>Twig example</a>");
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

	/**
	 * Twig-rendered example page.
	 *
	 * Resolves the Twig environment from the DI container and renders
	 * resources/views/hello.twig with a set of props passed as template variables.
	 *
	 * @param Twig $twig
	 * @param PathInterface $path
	 * @return ResponseInterface
	 */
	public function hello(Twig $twig, PathInterface $path): ResponseInterface
	{
		$name = $path->select("name")->last() ?: 'World';

		return $twig->render('hello.twig', [
			'title'       => 'Hello from Twig',
			'name'        => $name,
			'rendered_at' => Clock::value('now')->dateTime(),
			'items'       => ['Twig templates', 'PSR-7 responses', 'Dependency injection'],
		]);
	}
}
