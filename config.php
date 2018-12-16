<?php 

if (!defined('PTP_PROYECT')) {
	die('No access direct');
}

$app = [
	'url' => url(),
	'timezone' => 'America/Bogota',
];

$session = [
	'driver' => 'database',
	'table' => 'sessions',
];

$database = [
	'default' =>  env('DB_CONNECTION', 'mysql'),
	'table_migrations' => 'migrations',

	'connections' => [
		'mysql' => [
			'driver'    => 'mysql',
 			'host'      => env('DB_HOST', 'localhost'),
 			'database'  => env('DB_DATABASE', 'ptp'),
 			'username'  => env('DB_USERNAME', 'root'),
 			'password'  => env('DB_PASSWORD', 'mysql'),
 			'charset'   => 'utf8',
 			'collation' => 'utf8_unicode_ci',
 			'prefix'    => '',
		],
	]
];

$ptp = [
	'id' => env('PTP_ID', '6dd490faf9cb87a9862245da41170ff2'),
	'key' => env('PTP_KEY', '024h1IlD'),
	'wdsl' => env('PTP_WSDL', 'https://test.placetopay.com/soap/pse/?wsdl'),
	'endpoint' => env('PTP_ENDPOINT', 'https://test.placetopay.com/soap/pse'),
	'options' => [
		'cache_wsdl' => 0,
		'trace' => 1,
		'stream_context' => stream_context_create([
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		])
	]
];

return [
	'app' => $app,
	'ptp' => $ptp,
	'database' => $database,
	'session' => $session,
];