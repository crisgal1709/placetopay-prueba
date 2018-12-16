<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaysTable extends Migration
{

   public function up()
   {
         $schema = app()->db->getSchemaBuilder();
         $schema->create('pays', function($table){
            $table->increments('id');
            $table->string('returnCode',30);
            $table->string('bankURL',255)->nullable();
            $table->string('trazabilityCode',40)->nullable();
            $table->string('transactionCycle')->nullable();
            $table->integer('transactionID');
            $table->string('sessionID',32);
            $table->string('bankCurrency',3)->nullable();
            $table->float('bankFactor', 8, 2)->nullable();
            $table->integer('responseCode')->nullable();
            $table->string('responseReasonCode',3)->nullable();
            $table->string('responseReasonText',255)->nullable();
            $table->string('status',1)->nullable()->default('0');
            $table->timestamps();
         });
   }
}

