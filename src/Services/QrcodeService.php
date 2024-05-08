<?php

namespace App\Services;

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\PngWriter;

class QrcodeService
{
    private $qrCodeBuilder;

    public function __construct(BuilderInterface $qrCodeBuilder)
    {
        $this->qrCodeBuilder = $qrCodeBuilder;
    }

    public function generateQRCode($data): string
    {
        // Generate QR code
        $qrCodeResult = $this->qrCodeBuilder
            ->data($data)
            ->build();

        // Convert the QR code result to a string representation
        $qrCodeString = (new PngWriter())->writeString($qrCodeResult);

        return $qrCodeString;
    }
}
