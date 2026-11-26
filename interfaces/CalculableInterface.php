<?php
namespace App\Interfaces;

interface CalculableInterface {
    public function calculateTotal(float $quantity = 1.0): float;
    public function getFormattedPrice(): string;
}
