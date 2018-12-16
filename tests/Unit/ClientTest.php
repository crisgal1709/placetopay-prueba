<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Tests\CreateApplication;

class CientTest extends TestCase
{
	use CreateApplication;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetBankList()
    {
    	$app = $this->createApplication();

        $this->assertInstanceOf(
        		Collection::class,
        		$app->client->getBankList()
        	);
    }

    public function testCreateTransactionCodeSuccess()
    {
    	$app = $this->createApplication();

    	$this->assertEquals(
    		'SUCCESS',
    		$app->client->createTransaction($this->createTransactionForTest())->returnCode
    	);
    }

    public function testGetTransactionInformation()
    {
    	$app = $this->createApplication();

    	$this->assertInstanceOf(
        		\StdClass::class,
        		$app->client->getTransactionInformation(1465445378)
        	);
    }
}