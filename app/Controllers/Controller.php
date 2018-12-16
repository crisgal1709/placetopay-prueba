<?php 

namespace App\Controllers;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Exception;

class Controller {

	public $allowedMethod = 'GET';

	public function callAction($method, ...$parameters)
	{
		if (!method_exists($this, $method)) {
			if (request()->expectsJson()) {
				return ['error' => '404 not found'];
			}

			return redirect(url('error/notFound'));
			
		}

		return $this->{$method}(...$parameters);
	}

	public function setAllowedMethod($method = 'GET')
	{
		$this->allowedMethod = strtoupper($method);
	}

	public function ensureIsAllowedMethod($method)
	{
		$methodRequest = request()->method();

		if ($methodRequest == 'OPTIONS') {
			return true;
		}

		$this->allowedMethod = $method;

		if ( $this->allowedMethod == $methodRequest) {
			return true;
		} else {
			throw new Exception("METHOD NOT ALLOWED");
		}
	}

}