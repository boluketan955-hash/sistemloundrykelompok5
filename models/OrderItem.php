<?php
namespace App\Models;

use App\Interfaces\CalculableInterface;

class OrderItem implements CalculableInterface {
    private int $id;
    private LaundryService $service;
    private float $quantity;
    private float $subtotal;
    private string $notes;

    public function __construct(LaundryService $service, float $quantity, string $notes = '', int $id = 0) {
        $this->id = $id;
        $this->service = $service;
        $this->quantity = $quantity;
        $this->notes = $notes;
        $this->subtotal = $this->calculateTotal();
    }

    public function calculateTotal(float $quantity = 1.0): float {
        return $this->service->calculateTotal($this->quantity);
    }

    public function getFormattedPrice(): string {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getService(): LaundryService { return $this->service; }
    public function getQuantity(): float { return $this->quantity; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getNotes(): string { return $this->notes; }
}
