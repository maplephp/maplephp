<?php


return [
	'default' => env('DB_CONNECTION'),
	'connections' => [
		'mysql' => [
			'driver'   => 'pdo_mysql',
			'host'     => env('DB_HOST', '127.0.0.1'),
			'port'     => env('DB_PORT', 3306),
			'dbname'   => env('DB_DATABASE', ''),
			'user'     => env('DB_USERNAME', ''),
			'password' => env('DB_PASSWORD', ''),
			'charset'  => 'utf8mb4',
		],
		'sqlite' => [
			'driver' => 'pdo_sqlite',
			'file'   => env('DB_DATABASE', "database.sqlite"),
		],
		'test' => [
			'driver' => 'pdo_sqlite',
			'memory' => true,
		],
	],
];