<?php 

namespace Core;

use Carbon\Carbon;
use Core\Client;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;
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

class Ptp extends Container
{

	protected $namespaceControllers = 'App\Controllers';
	protected $defaultController = 'IndexController';
	protected $defaultAction = 'index';

	function __construct()
	{
		//Contancia para evitar que se pueda acceder a algunos archivos por medio del navegador
		define('PTP_PROYECT', '');

		//Se crea el contenedor principal y se inicializan las clases que se van a usar

		$this->instance('path', realpath(__DIR__.'/../'));

		$this->basePath = rtrim(realpath(__DIR__.'/../'), '\/');

		static::setInstance($this);

		$this->instance('app', $this);

		$this->instance(Container::class, $this);

		$this->bootstrap();
	}

	/**
     * Inicializa las clases en el contenedor principal
     *
     * @return Void
     */

	protected function bootstrap()
	{
		$this->loadEnviroment();
		$this->initConfiguration();
		$this->initDatabase();
		$this->initViews();
		$this->instantiator();
	}

	/**
     * Soporte para archivo de variables de entorno en .env
     *
     * @return void
     */

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
	/**
     * Inicializa las configuraciones globales y las ingresa en el contenedor en la clase Config 
     *
     * @return $this
     */

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

	/**
     * Configura los eventos y la base de datos así como el migrator 
     * para poder usar migraciones mediante archivos
     *
     * @return void
     */

	protected function initDatabase()
	{
		$providers = [
			EventServiceProvider::class,
			DatabaseServiceProvider::class,
		];
		
		foreach($providers as $provider)
		{
			try {
				$p = new $provider($this);

				if (method_exists($p, 'register')) {
					$p->register();
				}

				if (method_exists($p, 'boot')) {
					$p->boot();
				}
			} catch (Exception $e) {
				//Silence is golden
			}
		}

		$this->app->singleton('migrator', function($app){

			return new Migrator(
					new DatabaseMigrationRepository($app->db, $app['config']->get('database.table_migrations')), 
					$app->db, 
					$app['files']);
		});
		//$this->session->driver()->start();
	}

	/**
     * Inicializa la clase de las Vistas
     *
     * @return void
     */
	protected function initViews()
	{
		$view = new View($this);

		$view->setViewsPath($this->basePath . '/app/Views');

		$this->instance('view', $view);
	}

	/**
     * Instancia algunas clases en el contenedor
     *
     * @return void
     */

	protected function instantiator()
	{
		//Instancia del pretty Handler para las excepciones
		$whoops = new \Whoops\Run;
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		$whoops->register();

		$this->app->whoops = $whoops;

		//Handle el Filesystem nativo
		$this->singleton('files', function(){
			return new Filesystem;
		});
		
		//Instancia del Cliente usado para consumir el Web Service de PlaceToPay
		$this->singleton('client', function(){
			return new Client($this);
		});

		//Instancia de Carbon para manejo de fechas
		$this->singleton('carbon', function($app){
			return new Carbon();
		});
	}

	/**
     * Recibe el Request del navegador y lo procesa para devolver una respuesta
     *
     * @return void
     */

	public function run(Request $request)
	{
		$request->enableHttpMethodParameterOverride();
		$this->convertFiles($request);
		$response = $this->prepareResponse($this->exec($request));
		$this->setHeaders($response);
		return $response->prepare($request);
	}

	/**
     * Los archivos llegan como una instancia de 
     * Symfony\Component\HttpFoundation\File\UploadedFile;
     * Este método los convierte en instancias de
     * Illuminate\Http\UploadedFile;
     * @return void
     */

	public function convertFiles(Request &$request)
	{
		if ($request->files->count() > 0) {
			$request->files->replace($request->allFiles());
		}
	}

	/**
     * Recibe la respuesta que devuelven los controladores y la convierte en el
     * tipo de Respuesta que se considere necesario
     *
     * @return void
     */

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

	/**
     * Para no tener problemas con los Cors
     * Este método se usa para casos prácticos
     * PERO ES UNA MALA PRÁCITCA
     *
     * @return void
     */

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

	/**
     * Recibe el Request, Lo procesa y busca tanto el controlador como el método para 
     * devolver una respuesta
     * @return void
     */

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

	/**
     * Libera el contenedor luego de que se devuelve una respuesta
     *
     * @return void
     */

	public function terminate()
	{
		$this->flush();
	}

	public function loadTasks()
	{
		//
	}

}