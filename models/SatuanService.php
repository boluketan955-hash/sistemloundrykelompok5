<?php
namespace App\Models;

class SatuanService extends LaundryService {
    private string $itemCategory;
    private bool $specialCare;

    public function __construct(string $code, string $name, float $price, string $itemCategory = 'Pakaian Khusus', bool $specialCare = true, int $id = 0) {
        parent::__construct($code, $name, $price, $id);
        $this->itemCategory = $itemCategory;
        $this->specialCare = $specialCare;
    }

    public function calculateTotal(float $quantity = 1.0): float {
        $careFee = $this->specialCare ? 5000.0 : 0.0;
        return $quantity * ($this->basePrice + $careFee);
    }

    public function getEstimatedHours(): int {
        return 72;
    }

    public function getRuleDescription(): string {
        return 'Hitung per Pcs • Biaya perawatan khusus +Rp 5.000/item';
    }
}
