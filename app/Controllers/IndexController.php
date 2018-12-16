<?php 

namespace App\Controllers;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Http\Request;

class IndexController extends Controller {

	public function index()
	{
		$this->ensureIsAllowedMethod('GET');
		$banks = Bank::getBanks();
		return app()->view->render('index.php', ['banks' => $banks]);
		
	}

	public function prueba($id, $id2)
	{
		
	}

}