# Laravel PhonePe

[![Latest Stable Version](http://poser.pugx.org/dipesh79/laravel-phonepe/v)](https://packagist.org/packages/dipesh79/laravel-phonepe)
[![Total Downloads](http://poser.pugx.org/dipesh79/laravel-phonepe/downloads)](https://packagist.org/packages/dipesh79/laravel-phonepe)
[![License](http://poser.pugx.org/dipesh79/laravel-phonepe/license)](https://packagist.org/packages/dipesh79/laravel-phonepe)


This Laravel package allows you to integrate PhonePe payment on your Laravel Application.

## Usage/Examples

### Install Using Composer

```
composer require dipesh79/laravel-phonepe
```

### Add Variables in .env

```
PHONEPE_MERCHANT_ID="PGTESTPAYUAT"
PHONEPE_MERCHANT_USER_ID="MUID123"
PHONEPE_ENV="staging" //staging or production
PHONEPE_SALT_KEY="099eb0cd-02cf-4e2a-8aca-3e6c6aff0399"
PHONEPE_SALT_INDEX="1"
PHONEPE_CALLBACK_URL="http://localhost:8000"
```

### Publish Vendor File

```
php artisan vendor:publish
```

And publish "Dipesh79\LaravelPhonePe\LaravelPhonePeServiceProvider"

Redirect the user to payment page from your controller

```
use Dipesh79\LaravelPhonePe\LaravelPhonePe;


//Your Controller Method
public function phonePePayment()
{
    $phonepe = new LaravelPhonePe();
    //amount, phone number, callback url, unique merchant transaction id
    $url = $phonepe->makePayment(1000, '9999999999', 'https://locahost:8000/redirct-url','1');
    return redirect()->away($url);
}

```

### Check Payment Status

After Successful Payment PhonePe will redirect to your callback url with transaction id and status. You can check the
payment status using transaction id.

```
use Dipesh79\LaravelPhonePe\LaravelPhonePe;
use Illuminate\Http\Request;


public function callBackAction(Request $request)
{
 $phonepe = new LaravelPhonePe();
 $response = $phonepe->getTransactionStatus($request->all());
 if($response == true){
    //Payment Success
 }
 else
 {
    //Payment Failed           
 }
}
```

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Author

- [@Dipesh79](https://www.github.com/Dipesh79)

## Support

For support, email dipeshkhanal79[at]gmail[dot]com.

