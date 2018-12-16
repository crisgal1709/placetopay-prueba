<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model {

	/**
		 * Fields that can be mass assigned.
		 *
		 * @var array
		 */
		protected $fillable = [
			'returnCode',
			'bankURL',
			'trazabilityCode',
			'transactionCycle',
			'transactionID',
			'sessionID',
			'bankCurrency',
			'bankFactor',
			'responseCode',
			'responseReasonCode',
			'responseReasonText',
			'status',
		];

	public static function createData($result)
	{
		return static::create([
			'returnCode' => $result->returnCode,
			'bankURL' => $result->bankURL,
			'trazabilityCode' => $result->trazabilityCode,
			'transactionCycle' => $result->transactionCycle,
			'transactionID' => $result->transactionID,
			'sessionID' => $result->sessionID,
			'bankCurrency' => $result->bankCurrency,
			'bankFactor' => $result->bankFactor,
			'responseCode' => $result->responseCode,
			'responseReasonCode' => $result->responseReasonCode,
			'responseReasonText' => $result->responseReasonText,
		]);
	}

}