<?php
namespace App\Models;

use App\Exceptions\InvalidOrderException;

class Payment {
    private int $id;
    private string $paymentCode;
    private int $orderId;
    private float $amountPaid;
    private string $method;
    private string $paymentStatus;

    public function __construct(string $paymentCode, int $orderId, float $amountPaid, string $method = 'Tunai', string $status = 'Belum Lunas', int $id = 0) {
        $this->id = $id;
        $this->paymentCode = $paymentCode;
        $this->orderId = $orderId;
        $this->amountPaid = $amountPaid;
        $this->method = $method;
        $this->paymentStatus = $status;
    }

    public function calculateChange(float $totalBill): float {
        if ($this->amountPaid < $totalBill) {
            throw new InvalidOrderException('amount_paid', 'Nominal pembayaran kurang dari total tagihan.');
        }
        return $this->amountPaid - $totalBill;
    }

    public function processPayment(float $totalBill): bool {
        if ($this->amountPaid >= $totalBill && $totalBill > 0) {
            $this->paymentStatus = 'Lunas';
            return true;
        }
        return false;
    }

    public function getReceiptData(): array {
        return [
            'payment_code' => $this->paymentCode,
            'order_id' => $this->orderId,
            'amount_paid' => $this->amountPaid,
            'method' => $this->method,
            'status' => $this->paymentStatus
        ];
    }
}
