# Coinpay

Safe, fast and instant payments; Anytime, anywhere with CoinPay

## Installation

Install with composer:

```bash
composer require coinpay/php-integration
```

## Usage
```php
use Coinpay\Finance\CoinPayGateway;

try {
    $gateway = new CoinPayGateway("###COINPAY_API_KEY###");
    $response = $gateway->request(
        1,                                                              // The amount to be paid (in Dollar - $).
        'https://your-callback.url',                                    // The URL the user will be redirected to after payment.
        'ref123',                                                       // A unique reference ID for tracking the transaction.
        'payer@example.com',                                            // The identity of the payer (email or phone number).
        'Alimo',                                                        // Full name of the payer.
        'Test Payment',                                                 // Description of the payment (e.g. "Payment for order #123").
        '1234567890'                                                    // National identification code of the payer.
    );

    echo "Payment URL: " . $response->url . PHP_EOL;                    //Redirect user to payment gateway url
    echo "Transaction ID: " . $response->transactionId . PHP_EOL;       //Store this Id if you need
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}