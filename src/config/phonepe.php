<?php

return array(
    'merchantId' => env('PHONEPE_MERCHANT_ID'),
    'merchantUserId' => env('PHONEPE_MERCHANT_USER_ID'),
    'env' => env('PHONEPE_ENV'),
    'saltKey' => env('PHONEPE_SALT_KEY'),
    'saltIndex' => env('PHONEPE_SALT_INDEX'),
    'redirectUrl' => env('PHONEPE_REDIRECT_URL'),
    'callBackUrl' => env('PHONEPE_CALLBACK_URL')
);
