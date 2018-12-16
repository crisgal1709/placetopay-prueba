<?php 

namespace App\Controllers;

class MigrateController extends Controller
{

	public function index()
	{
		$this->ensureIsAllowedMethod('GET');
	}

	public function getBasePath($path = '')
	{
		$this->ensureIsAllowedMethod('GET');
		return app()->basePath . '/migrations_files/' . $path;
	}

	public function install()
	{
		$this->ensureIsAllowedMethod('GET');
		if (!app()->db->getSchemaBuilder()->hasTable('migrations')) {
			return app()->migrator->getRepository()->createRepository();
		}
	}

	public function up()
	{
		$this->ensureIsAllowedMethod('GET');
		$path = $this->getBasePath();

		return app()->migrator->run([$path]);
	}
}