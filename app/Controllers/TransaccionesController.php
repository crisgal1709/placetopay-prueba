<?php 

namespace App\Controllers;

use App\Models\Pay;
use Illuminate\Support\Str;

class TransaccionesController extends Controller 
{
	public function index()
	{
        $this->ensureIsAllowedMethod('GET');
		return app()->view->render('transacciones.php');
	}

    public function listado()
    {
        $this->ensureIsAllowedMethod('GET');

        $request = request();
        $conteo = 0;
        $data = [];
        $search = $request->search['value'];

        $prePays = Pay::query();

        if (empty($search)) {
            $conteo = $prePays->count();
            $pays = $prePays->limit($request->length)
                            ->offset($request->start)
                            ->get();
        } else {
            $pays = Pay::where('transactionID', 'LIKE', '%'.$search.'%')
                        ->orWhere('responseReasonText', 'LIKE', '%'.$search.'%')
                        ->orWhere('id', 'LIKE', '%'.$search.'%')
                        ->orWhere('created_at', 'LIKE', '%'.$search.'%')
                        ->get();

            $conteo = $pays->count();
        }

        foreach($pays as $pay){
            $_data = [
                $pay->id,
                $pay->transactionID,
                $pay->responseReasonText,
                $pay->created_at->format('d-m-Y h:m a'),
            ];

            array_push($data, $_data);
        }

        $return = [
            "draw" => $request->draw,
            "recordsTotal" => $conteo,
            "recordsFiltered" => $conteo,
            "data" => $data,
        ];

        return $return;
    }

	public function store()
	{
        $this->ensureIsAllowedMethod('POST');
		$request = request();

		$person = [
            "document"     => $request->document,
            "documentType" => $request->documentType,
            "firstName"    => $request->firstName,
            "lastName"     => $request->lastName,
            "company"      => $request->company,
            "emailAddress" => $request->emailAddress,
            "address"	   => $request->address,
            "city"         => $request->city,
            "province"     => $request->province,
            "country"	   => $request->country,
            "phone"		   => $request->phone,
            "mobile"	   => $request->mobile
        ];

        $shipping = [
    		'document'     => '43817979',
    		'documentType' => 'CC',
    		'firstName'    => 'Rosa Elena',
    		'lastName'	   => 'Galeano',
    		'company'	   => 'House Inc',
    		'emailAddress' => 'rosa@gmail.com',
    		'address'	   => 'carrera 46 # 100-10',
    		'city'	       => 'Medellin',
    		'province'     => 'Antioquia',
    		'country'	   => 'CO',
    		'phone'		   => '3010292010',
    		'mobile'	   => '3102093920',
    	];

        $transaction= [
            "bankCode"		 => $request->bankCode,
            "bankInterface"  => $request->bankInterface,
            "returnURL"      => url('transacciones/callback'),
            "reference" 	 => 'PUS' . Str::random(16),
            "description"    => 'Transacción de Prueba',
            "language"       => 'ES',
            "currency"		 => 'COP',
            "totalAmount"	 => (double) 50000,
            "taxAmount"		 => (double) 1000,
            "devolutionBase" => (double)0,
            "tipAmount"      => (double)0,
            "payer"		     => $person,
            "buyer"          => $person,
            "shipping"       => $shipping,
            "ipAddress"      => request()->getClientIp(),
            "userAgent"      => request()->userAgent(),
            "additionalData" => [],
        ];

        $result = app()->client->createTransaction($transaction);

        if (is_array($result) && isset($result['error'])) {
            app()->view->shareSession(['error' => $result]);
            return redirect(url('transacciones/error'));
        }

        if ($result->returnCode == 'SUCCESS') {
        	$pay = Pay::createData($result);

            $_SESSION['pay'] = serialize($pay);
        	
        	return redirect($result->bankURL);
        }

	}

	public function callback()
	{
        $this->ensureIsAllowedMethod('GET');

        $pay = unserialize($_SESSION['pay']);
        $result = app()->client->getTransactionInformation($pay->transactionID);
        //dd($result);

        if ($result->returnCode == 'SUCCESS') {
            $pay->update([
                'responseReasonText' => $result->responseReasonText,
                'responseReasonCode' => $request->responseReasonCode,
                'responseCode' => $result->responseCode,
                'transactionCycle' => $result->transactionCycle,
            ]);

            $_SESSION['pay'] = serialize($pay);

            return redirect(url('transacciones/success'));
        }
	}

    public function success()
    {
        $this->ensureIsAllowedMethod('GET');

        $pay = unserialize($_SESSION['pay']);

        return app()->view->render('success.php', ['pay' => $pay]);
    }

    public function error()
    {
        $this->ensureIsAllowedMethod('GET');

        return app()->view->render('error.php');
    }
}