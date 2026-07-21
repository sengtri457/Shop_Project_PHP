<?php

namespace App\Services;

use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QROptions;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Helpers\Utils;
use KHQR\Models\MerchantInfo;

class BakongService
{
    private string $merchantId;
    private string $accountId;
    private string $merchantName;
    private string $merchantCity;
    private string $accessToken;
    private string $apiBaseUrl;

    public function __construct()
    {
        $this->merchantId   = $_ENV['BAKONG_MERCHANT_ID'] ?? getenv('BAKONG_MERCHANT_ID') ?: '1456';
        $this->accountId    = $_ENV['BAKONG_ACCOUNT_ID'] ?? getenv('BAKONG_ACCOUNT_ID') ?: 'bun_sengtri@bkrt';
        $this->merchantName = $_ENV['BAKONG_MERCHANT_NAME'] ?? getenv('BAKONG_MERCHANT_NAME') ?: 'SENGTREE bUN';
        $this->merchantCity = $_ENV['BAKONG_MERCHANT_CITY'] ?? getenv('BAKONG_MERCHANT_CITY') ?: 'Phnom Penh';
        $this->accessToken  = $_ENV['BAKONG_ACCESS_TOKEN'] ?? getenv('BAKONG_ACCESS_TOKEN') ?: '';
        $envBaseUrl         = $_ENV['BAKONG_API_URL'] ?? getenv('BAKONG_API_URL') ?: 'https://api-bakong.nbc.gov.kh';
        $this->apiBaseUrl   = rtrim($envBaseUrl, '/') . '/v1';
    }

    private function addExpirationTimestamp(string $khqrString): string
    {
        $crcSepPos = strpos($khqrString, '6304');
        if ($crcSepPos === false) {
            throw new \RuntimeException('Invalid KHQR: CRC marker not found');
        }

        $dataPart = substr($khqrString, 0, $crcSepPos);
        $oldCrc = substr($khqrString, $crcSepPos + 4);

        $tag99Pos = strrpos($dataPart, '99');
        if ($tag99Pos === false) {
            throw new \RuntimeException('Invalid KHQR: Tag 99 not found');
        }

        $tag99Len = (int) substr($dataPart, $tag99Pos + 2, 2);
        $tag99Val = substr($dataPart, $tag99Pos + 4, $tag99Len);

        $sub00Tag = substr($tag99Val, 0, 2);
        $sub00Len = (int) substr($tag99Val, 2, 2);
        $sub00Val = substr($tag99Val, 4, $sub00Len);

        $expMs = floor(microtime(true) * 1000) + 900000;
        $expStr = (string) $expMs;
        $newTag99Val = '00' . sprintf('%02d', $sub00Len) . $sub00Val
                     . '01' . sprintf('%02d', strlen($expStr)) . $expStr;
        $newTag99Len = strlen($newTag99Val);
        $newTag99 = '99' . sprintf('%02d', $newTag99Len) . $newTag99Val;

        $newDataPart = substr($dataPart, 0, $tag99Pos) . $newTag99;
        $newQrToCrc = $newDataPart . '6304';
        $newCrc = Utils::crc16($newQrToCrc);

        return $newDataPart . '6304' . $newCrc;
    }

    public function getConfig(): array
    {
        return [
            'merchant_id'   => $this->merchantId,
            'account_id'    => $this->accountId,
            'merchant_name' => $this->merchantName,
            'merchant_city' => $this->merchantCity,
        ];
    }

    public function generateKhqr(int $orderId, float $amount, string $currency = 'USD'): array
    {
        $isKhr = strtoupper($currency) === 'KHR';
        $billNumber = 'ORD' . str_pad((string)$orderId, 6, '0', STR_PAD_LEFT);
        $finalAmount = $isKhr ? round($amount * 4100) : $amount;
        $currencyCode = $isKhr ? KHQRData::CURRENCY_KHR : KHQRData::CURRENCY_USD;

        $merchantInfo = new MerchantInfo(
            bakongAccountID: $this->accountId,
            merchantName: $this->merchantName,
            merchantCity: $this->merchantCity,
            merchantID: $this->merchantId,
            acquiringBank: 'National Bank of Cambodia',
            mobileNumber: '85599706869',
            currency: $currencyCode,
            amount: $finalAmount,
            billNumber: $billNumber,
        );

        $response = BakongKHQR::generateMerchant($merchantInfo);

        if ($response->status['code'] !== 0 || !$response->data) {
            throw new \RuntimeException($response->status['message'] ?? 'Failed to generate KHQR');
        }

        $fullKhqrString = $response->data['qr'];

        $fullKhqrString = $this->addExpirationTimestamp($fullKhqrString);

        $md5Hash = md5($fullKhqrString);

        $qrOptions = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64'    => true,
            'eccLevel'        => 'M',
            'scale'           => 10,
        ]);
        $qrCode = new \chillerlan\QRCode\QRCode($qrOptions);
        $qrImage = $qrCode->render($fullKhqrString);

        return [
            'order_id'       => $orderId,
            'bill_number'    => $billNumber,
            'amount'         => $finalAmount,
            'currency'       => strtoupper($currency),
            'account_id'     => $this->accountId,
            'merchant_name'  => $this->merchantName,
            'merchant_city'  => $this->merchantCity,
            'qr_string'      => $fullKhqrString,
            'qr_image'       => $qrImage,
            'md5'            => $md5Hash,
        ];
    }

    public function checkTransactionByMd5(string $md5Hash): array
    {
        if (empty($this->accessToken)) {
            return [
                'status'  => 'pending',
                'paid'    => false,
                'message' => 'Bakong access token not configured',
            ];
        }

        $url = $this->apiBaseUrl . '/check_transaction_by_md5';
        $payload = json_encode(['md5' => $md5Hash]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accessToken,
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            return [
                'status'   => 'pending',
                'paid'     => false,
                'http_code'=> $httpCode,
                'message'  => $error ?: 'Bakong API request failed or pending payment',
            ];
        }

        $data = json_decode($response, true);

        if (isset($data['responseCode']) && (int)$data['responseCode'] === 0) {
            return [
                'status'  => 'paid',
                'paid'    => true,
                'data'    => $data['data'] ?? [],
                'message' => 'Payment successfully verified by Bakong',
            ];
        }

        return [
            'status'  => 'pending',
            'paid'    => false,
            'data'    => $data,
            'message' => $data['responseMessage'] ?? 'Payment pending scan',
        ];
    }
}
