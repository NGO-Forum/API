<?php

namespace App\Services;

class KhqrService
{
    protected $mid;
    protected $tid;
    protected $merchantName;

    public function __construct()
    {
        $this->mid = env('KHQR_MID');
        $this->tid = env('KHQR_TID');
        $this->merchantName = env('KHQR_NAME');
    }

    public function generate($amountUSD, $transactionId)
    {
        $amount = number_format($amountUSD, 2, '.', '');

        $payload = [
            "002" => [
                "00" => "kh.gov.nbc.payment",
                "01" => "PAYMENT",
                "02" => [
                    "00" => "NBC",
                    "01" => $this->merchantName,
                    "02" => $this->mid,
                    "03" => $this->tid,
                ],
            ],
            "010" => $amount,
            "011" => "USD",
            "012" => $transactionId,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->generate($json);

        return base64_encode($svg);
    }
}
