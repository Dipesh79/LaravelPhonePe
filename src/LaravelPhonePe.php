<?php

namespace Dipesh79\LaravelPhonePe;

use Dipesh79\LaravelPhonePe\Exception\InvalidEnvironmentVariableException;
use Dipesh79\LaravelPhonePe\Exception\PhonePeException;

class LaravelPhonePe
{
    private mixed $merchantId;
    private mixed $merchantUserId;
    private string $baseUrl;
    private mixed $saltKey;
    private mixed $saltIndex;
    private mixed $callBackUrl;

    /**
     * @throws InvalidEnvironmentVariableException
     */
    public function __construct()
    {
        $this->merchantId = config('phonepe.merchantId');
        $this->merchantUserId = config('phonepe.merchantUserId');
        $this->baseUrl = config('phonepe.env') == 'production' ? 'https://api.phonepe.com/apis/hermes' : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        $this->saltKey = config('phonepe.saltKey');
        $this->saltIndex = config('phonepe.saltIndex');
        $this->callBackUrl = config('phonepe.callBackUrl');
        $this->checkEnvironment();
    }

    /**
     * @throws InvalidEnvironmentVariableException
     */
    public function checkEnvironment(): void
    {
        if ($this->merchantId == null || $this->merchantId == '') {
            throw new InvalidEnvironmentVariableException("Merchant Id is not added in .env file");
        }
        if ($this->merchantUserId == null || $this->merchantUserId == '') {
            throw new InvalidEnvironmentVariableException("Merchant User Id is not added in .env file");
        }
        if ($this->saltKey == null || $this->saltKey == '') {
            throw new InvalidEnvironmentVariableException("Salt Key is not added in .env file");
        }
        if ($this->saltIndex == null || $this->saltIndex == '') {
            throw new InvalidEnvironmentVariableException("Salt Index is not added in .env file");
        }
        if ($this->callBackUrl == null || $this->callBackUrl == '') {
            throw new InvalidEnvironmentVariableException("Call Back Url is not added in .env file");
        }
    }

    /**
     * @throws PhonePeException
     */
    public function makePayment($amount, $phone, $redirectUrl, $merchantTransactionId): string
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
        if ($rData->success) {
            return $rData->data->instrumentResponse->redirectInfo->url;
        } else {
            throw new PhonePeException($rData->message);
        }
    }

    public function getTransactionStatus(array $request): bool
    {

        $finalXHeader = hash('sha256','/pg/v1/status/' . $request['merchantId'] . '/' . $request['transactionId'] . $this->saltKey) . '###' . $this->saltIndex;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/pg/v1/status/' . $request['merchantId'] . '/' . $request['transactionId'],
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
                'X-VERIFY: ' . $finalXHeader,
                'X-MERCHANT-ID: ' . $request['transactionId']
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (json_decode($response)->success) {
            return true;
        } else {
            return false;
        }
    }
}
