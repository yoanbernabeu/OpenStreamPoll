<?php

declare(strict_types=1);

namespace App\Service\Qr;

use chillerlan\QRCode\QRCode;

class QrService
{
    public function generateQrCode(string $url): string
    {
        $qrcode = new QRCode();

        return $qrcode->render($url);
    }
}
