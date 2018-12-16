<?php 
session_start();
date_default_timezone_set('America/Bogotá');
mb_internal_encoding('UTF-8');

use Core\Ptp;

require __DIR__.'/vendor/autoload.php';

$app = new Ptp;

$app->loadTasks();

$response = $app->run(
	\Illuminate\Http\Request::capture()
)->send();



