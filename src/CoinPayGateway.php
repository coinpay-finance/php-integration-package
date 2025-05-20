<?php

namespace Coinpay\Finance;


class CoinPayGateway
{
    protected static $PREFIX = 'https://platform.coinpay.finance/api/v1/coin-pay';
    protected $coinPayApiKey = '';
    public function __construct($coinPayApiKey)
    {
        $this->coinPayApiKey = $coinPayApiKey;
    }

    /**
     * Send a payment request to CoinPay API.
     *
     * @param int $amount The amount to be paid (in Dollar - $).
     * @param string $callbackUrl The URL the user will be redirected to after payment.
     * @param string $client_ref_id A unique reference ID for tracking the transaction.
     * @param string|null $payer_identity The identity of the payer (email or phone number).
     * @param string|null $name Full name of the payer.
     * @param string|null $description Description of the payment (e.g. "Payment for order #123").
     * @param string|null $national_code National identification code of the payer.
     *
     * @return CoinPayPaymentResponse The response from the CoinPay API, including a payment URL.
     *
     * @throws \Exception If the request fails or the API returns an error.
     */
    public function request(
        int $amount,
        string $callbackUrl,
        string $client_ref_id,
        string $payer_identity = null,
        string $name = null,
        string $description = null,
        string $national_code = null
    ): CoinPayPaymentResponse
    {
        $data = [
            'amount' => $amount,
            'redirect_url' => $callbackUrl,
            'client_ref_id' => $client_ref_id,
        ];

        if (!empty($payer_identity)) {
            $data['payer_identity'] = $payer_identity;
        }
        if (!empty($name)) {
            $data['name'] = $name;
        }
        if (!empty($description)) {
            $data['description'] = $description;
        }
        if (!empty($national_code)) {
            $data['national_code'] = $national_code;
        }

        $jsonData = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::$PREFIX . '/payment');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // هدرها
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $this->coinPayApiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Curl error: " . $error_msg);
        }

        curl_close($ch);

        $responseBody = json_decode($response, true);

        if ($httpCode === 200 && is_array($responseBody) && !empty($responseBody['status']) && !empty($responseBody['url'])) {
            return new CoinPayPaymentResponse($responseBody['url'], $responseBody['transaction_id'] ?? 0);
        }

        throw new \Exception($responseBody['message'] ?? 'Payment request failed', $httpCode);
    }

}