<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBanksTable extends Migration
{
	public function up()
	{
		$schema = app()->db->getSchemaBuilder();
		$schema->create('banks', function($table){
			$table->increments('id');
			$table->string('bankCode', 100)->nullable();
			$table->string('bankName')->nullable();
			$table->timestamps();
		});
	}
}