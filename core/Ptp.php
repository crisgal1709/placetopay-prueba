<?php 

namespace Core;

use Core\Client;
use Dotenv\Dotenv;
use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\SessionServiceProvider;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;

class Ptp extends Container
{

	protected $namespaceControllers = 'App\Controllers';
	protected $defaultController = 'IndexController';
	protected $defaultAction = 'index';

	function __construct()
	{
		define(PTP_PROYECT, '');

		$this->instance('path', realpath(__DIR__.'/../'));

		$this->basePath = rtrim(realpath(__DIR__.'/../'), '\/');

		static::setInstance($this);

		$this->instance('app', $this);

		$this->instance(Container::class, $this);

		$this->bootstrap();
	}


	protected function bootstrap()
	{
		$this->loadEnviroment();
		$this->initConfiguration();
		$this->initDatabase();
		$this->initViews();
		$this->instantiator();
	}

	protected function loadEnviroment()
	{
		try {
            $env = new Dotenv($this->basePath, '.env');
            $this->instance('env', $env);
            $env->load();
        } catch (InvalidPathException $e) {
            //
        } catch (InvalidFileException $e) {
            die('The environment file is invalid: '.$e->getMessage());
        }

	}

	protected function initConfiguration()
	{
		$configuration = require $this->basePath . DIRECTORY_SEPARATOR . 'config.php';

		$config = new Config([]);

		foreach($configuration as $key => $value){
			$config->set($key, $value);
		}

		$this->instance('config', $config);

		return $this;
	}

	protected function initDatabase()
	{
		(new EventServiceProvider($this))->register();
		(new DatabaseServiceProvider($this))->register();
		(new DatabaseServiceProvider($this))->boot();
		//(new SessionServiceProvider($this))->register();
		$this->db->statement('select * from sessions');

		$this->app->singleton('migrator', function($app){

			return new Migrator(
					new DatabaseMigrationRepository($app->db, $app['config']->get('database.table_migrations')), 
					$app->db, 
					$app['files']);
		});
		//$this->session->driver()->start();
	}

	protected function initViews()
	{
		$view = new View($this);

		$view->setViewsPath($this->basePath . '/app/Views');

		$this->instance('view', $view);
	}

	protected function instantiator()
	{
		$whoops = new \Whoops\Run;
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$whoops->register();

		$this->app->whoops = $whoops;

		$this->singleton('files', function(){
			return new Filesystem;
		});
		
		$this->singleton('client', function(){
			return new Client($this);
		});
	}

	public function run(Request $request)
	{
		$request->enableHttpMethodParameterOverride();
		$response = $this->prepareResponse($this->exec($request));
		$this->setHeaders($response);
		return $response->prepare($request);
	}

	public function prepareResponse($response)
	{
		if ($response instanceof RedirectResponse) {
			//$response->setContent(null);
		}
		if ($response instanceof Response) {
			return $response;
		}

		if (($response instanceof Arrayable) 
				|| $response instanceof Jsonable
				|| $this->request->ajax()
			) {
			return new JsonResponse($response);
		}
		return new Response($response);
	}

	protected function setHeaders(&$response)
	{
		if ($response instanceof Response) {
			$response->header('Access-Control-Allow-Origin', '*');
			$response->header('Access-Control-Allow-Headers', 'Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Allow-Request-Method');
			$response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
			$response->header('Allow', 'GET, POST, OPTIONS, PUT, DELETE');
		}

		return $response;	
	}

	public function exec(Request $request)
	{
		$this->instance('request', $request);
		$uri = $request->segments();
		$count = count($uri);

		switch ($count) {
			case 0:
				try {
					$indexController = $this->namespaceControllers . '\\' . $this->defaultController;
					if (!class_exists($indexController)) {
						return redirect(url('error/notFound'));
					}
					return (new $indexController)->index();
				} catch (\Exception $e) {
					
				}
				break;
			
			case 1:
				try {
					$controller = $this->namespaceControllers . '\\' . ucWords($uri[0] . 'Controller');
					if (!class_exists($controller)) {
						return redirect(url('error/notFound'));
					}
					return (new $controller)->callAction($this->defaultAction, []);
				} catch (\Exception $e) {
					
				}
				break;
			case 2:
				try {
					$controller = $this->namespaceControllers . '\\' . ucWords($uri[0] . 'Controller');
					$method = $uri[1];
					if (!class_exists($controller)) {
						return redirect(url('error/notFound'));
					}
					return (new $controller)->callAction($method, []);
				} catch (\Exception $e) {
					
				}
				break;
		}

		$controller = $this->namespaceControllers . '\\' . ucWords($uri[0] . 'Controller');
		$method = $uri[1];

		unset($uri[0], $uri[1]);

		return (new $controller)->callAction($method, ...$uri);

	}

	public function terminate()
	{
		$this->flush();
	}

	public function loadTasks()
	{
		//
	}

}