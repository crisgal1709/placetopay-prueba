<?php

namespace App\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{

	public function notFound()
	{
		$this->ensureIsAllowedMethod('GET');
		return app()->view->render('404.php');
	}

}