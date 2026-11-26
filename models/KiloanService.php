<?php
namespace App\Models;

class KiloanService extends LaundryService {
    private float $minWeight = 2.0;
    private string $fragrance;

    public function __construct(string $code, string $name, float $price, string $fragrance = 'Lavender', int $id = 0) {
        parent::__construct($code, $name, $price, $id);
        $this->fragrance = $fragrance;
    }

    public function calculateTotal(float $quantity = 2.0): float {
        $effectiveWeight = max($this->minWeight, $quantity);
        return $effectiveWeight * $this->basePrice;
    }

    public function getEstimatedHours(): int {
        return 48;
    }

    public function getRuleDescription(): string {
        return 'Minimal berat 2.0 Kg • Parfum ' . $this->fragrance;
    }
}
