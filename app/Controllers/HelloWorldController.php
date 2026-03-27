<?php

declare(strict_types=1);

namespace App\Controllers;

use MaplePHP\Core\Render\StaticRenderer;
use MaplePHP\Core\Support\Twig;
use MaplePHP\DTO\Format\Clock;
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
	 * @param StaticRenderer $render
	 * @return ResponseInterface
	 */
	public function index(ResponseInterface $response, StaticRenderer $render): ResponseInterface
	{
		$html = $render->welcome();
		$response->getBody()->write($html);
		return $response;
	}

	/**
	 * Twig-rendered example page.
	 * Renders resources/views/hello.twig with a set of props passed as template variables.
	 *
	 * @param Twig $twig
	 * @param PathInterface $path
	 * @throws \Exception
	 */
	public function show(Twig $twig, PathInterface $path): void
	{
		$name = $path->select("name")->last() ?: 'World';

		$twig->render('views/hello.twig', [
			'title' => 'Hello from Twig',
			'name' => $name,
			'rendered_at' => Clock::value('now')->dateTime(),
			'items' => ['Twig templates', 'PSR-7 responses', 'Dependency injection'],
		]);
	}
}
