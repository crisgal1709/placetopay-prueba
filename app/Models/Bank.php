<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'banks';

	/**
	 * @var array
	 */
	protected $fillable = ['bankCode', 'bankName'];


	public function saveBanks($response)
	{
		$response;
	}

	public static function getBanks()
	{
		$data = static::all();

		if ($data->count() == 0) {
			$banks = app()->client->getBankList();
			foreach($banks as $bank){
				static::create((array)$bank);
			}

			return static::all();

		} else {
			return $data;
		}
	}
}