<?php 

use Illuminate\Container\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

if (!function_exists('app')) {
	
	function app()
	{
		return Container::getInstance();
	}
}

if (!function_exists('request')) {
	
	function request()
	{
		try {
			return app()->request;
		} catch (Exception $e) {
			return Request::capture();
		}
		
	}
}

if (!function_exists('url')) {
	
	function url($path = '')
	{
		try {
			return app()->request->root() . '/' . $path;
		} catch (Exception $e) {
			$request = Request::capture();
			return $request->root() . '/' . $path;
		}
	}
}

if (!function_exists('redirect')) {

	function redirect($path, $status = 301, $headers = [])
	{
		$response = new RedirectResponse($path, $status, $headers);
		$response->setRequest(request());
		return $response;
	}
}