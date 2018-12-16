<?php 

namespace Core;

use Illuminate\Container\Container;
use SoapClient;

class Client {

	public $app;
	public $soapClient;
	public $config;

	function __construct(Container $app)
	{
		$this->app = $app;
		$config = $app->config->get('ptp');
		$this->config = $config;
		$client = new SoapClient($config['wdsl'], $config['options']);

		$this->soapClient = $client;
		return $this;
	}

	public function auth()
	{
		$seed = date('c');

		return [
			'login' => $this->config['id'],
			'tranKey' => sha1($seed . $this->config['key'], false ),
			'seed' => $seed,
			'adittional' => '',
		];
	}

	public function getBankList()
	{
		return collect($this->soapClient->getBankList([
			'auth' => $this->auth(),
		])->getBankListResult)->collapse();
	}

	public function createTransaction($transaction = [])
	{
		try {
			return $this->soapClient->createTransaction(
				[
					'auth' => $this->auth(),
					'transaction' => $transaction
				]
			)->createTransactionResult;
		} catch (\SoapFault $e) {
			return [
				'error' => 1,
				'message' => $e->getMessage(),
			];
		}
	}

	public function getTransactionInformation($transactionID)
	{
		return $this->soapClient->getTransactionInformation(
			[
				'auth' => $this->auth(),
				'transactionID' => $transactionID
			]
		)->getTransactionInformationResult;
	}

}