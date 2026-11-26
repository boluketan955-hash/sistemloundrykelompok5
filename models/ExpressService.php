<?php
namespace App\Models;

class ExpressService extends LaundryService {
    private float $expressMultiplier;
    private int $priorityLevel;

    public function __construct(string $code, string $name, float $price, float $multiplier = 1.25, int $priorityLevel = 1, int $id = 0) {
        parent::__construct($code, $name, $price, $id);
        $this->expressMultiplier = $multiplier;
        $this->priorityLevel = $priorityLevel;
    }

    public function calculateTotal(float $quantity = 1.0): float {
        return $quantity * $this->basePrice * $this->expressMultiplier;
    }

    public function getEstimatedHours(): int {
        return 6;
    }

    public function getRuleDescription(): string {
        return 'Layanan Kilat 6 Jam • Tarif prioritas 1.25x harga dasar';
    }
}
