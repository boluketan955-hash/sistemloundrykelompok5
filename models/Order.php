<?php
namespace App\Models;

use App\Interfaces\IdentifiableInterface;
use App\Interfaces\CalculableInterface;
use App\Interfaces\NotifiableInterface;
use App\Exceptions\InvalidOrderException;

class Order implements IdentifiableInterface, CalculableInterface, NotifiableInterface {
    private int $id;
    private string $orderCode;
    private Customer $customer;
    private Staff $staff;
    /** @var OrderItem[] */
    private array $items = [];
    private string $status;
    private float $totalAmount = 0.0;
    private string $notes;
    private string $orderDate;

    public const VALID_STATUSES = ['Diterima', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai'];

    public function __construct(
        string $orderCode,
        Customer $customer,
        Staff $staff,
        string $status = 'Diterima',
        string $notes = '',
        int $id = 0
    ) {
        $this->id = $id;
        $this->orderCode = $orderCode;
        $this->customer = $customer;
        $this->staff = $staff;
        $this->setStatus($status);
        $this->notes = $notes;
        $this->orderDate = date('Y-m-d H:i:s');
    }

    public function addItem(OrderItem $item): void {
        if ($item->getQuantity() <= 0) {
            throw new InvalidOrderException('quantity', 'Jumlah berat atau item laundry harus lebih dari 0.');
        }
        $this->items[] = $item;
        $this->calculateTotal();
    }

    public function calculateTotal(float $quantity = 1.0): float {
        $sum = 0.0;
        foreach ($this->items as $item) {
            $sum += $item->getSubtotal();
        }
        $this->totalAmount = $sum;
        return $this->totalAmount;
    }

    public function getFormattedPrice(): string {
        return 'Rp ' . number_format($this->totalAmount, 0, ',', '.');
    }

    public function setStatus(string $status): void {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidOrderException('status', "Status pesanan '{$status}' tidak valid.");
        }
        $this->status = $status;
    }

    public function sendNotification(string $message): bool {
        return $this->customer->sendNotification("[{$this->orderCode}] {$message}");
    }

    public function getNotificationTarget(): string {
        return $this->customer->getNotificationTarget();
    }

    public function getId(): int { return $this->id; }
    public function getCode(): string { return $this->orderCode; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getStaff(): Staff { return $this->staff; }
    public function getItems(): array { return $this->items; }
    public function getStatus(): string { return $this->status; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function getNotes(): string { return $this->notes; }
}
