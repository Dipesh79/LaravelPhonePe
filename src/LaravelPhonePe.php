<?php

namespace Dipesh79\LaravelPhonePe;

use Illuminate\Http\Request;

class LaravelPhonePe
{
    private $merchantId;
    private $merchantUserId;
    private $baseUrl;
    private $saltKey;
    private $saltIndex;
    private $callBackUrl;

    public function __construct()
    {
        $this->merchantId = config('phonepe.merchantId');
        $this->merchantUserId = config('phonepe.merchantUserId');
        $this->baseUrl = config('phonepe.env') == 'production' ? 'https://api.phonepe.com/apis/hermes' : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        $this->saltKey = config('phonepe.saltKey');
        $this->saltIndex = config('phonepe.saltIndex');
        $this->callBackUrl = config('phonepe.callBackUrl');
    }

    public function makePayment($amount, $phone,$redirectUrl,$merchantTransactionId)
    {
        $data = array(
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => $merchantTransactionId,
            'merchantUserId' => $this->merchantUserId,
            'amount' => $amount * 100,
            'redirectUrl' => $redirectUrl,
            'redirectMode' => 'POST',
            'callbackUrl' => $this->callBackUrl,
            'mobileNumber' => $phone,
            'paymentInstrument' =>
                array(
                    'type' => 'PAY_PAGE',
                ),
        );

        $encode = base64_encode(json_encode($data));

        $string = $encode . '/pg/v1/pay' . $this->saltKey;
        $sha256 = hash('sha256', $string);
        $finalXHeader = $sha256 . '###' . $this->saltIndex;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/pg/v1/pay',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(['request' => $encode]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-VERIFY: ' . $finalXHeader
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $rData = json_decode($response);
        return $rData->data->instrumentResponse->redirectInfo->url;


    }

    public function getTransactionStatus(array $request)
    {

        $finalXHeader = hash('sha256','/pg/v1/status/'.$request['merchantId'].'/'.$request['transactionId'].$this->saltKey).'###'.$this->saltIndex;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl.'/pg/v1/status/'.$request['merchantId'].'/'.$request['transactionId'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'accept: application/json',
                'X-VERIFY: '.$finalXHeader,
                'X-MERCHANT-ID: '.$request['transactionId']
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (json_decode($response)->success) {
            return true;
        }
        else
        {
            return false;
        }
    }


}
