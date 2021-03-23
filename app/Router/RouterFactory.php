<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');

		$router->addRoute('<presenter>/login<action>[/<id>]', 'Login:default');
		$router->addRoute('<presenter>/register<action>[/<id>]', 'Register:default');

		return $router;
	}
}
