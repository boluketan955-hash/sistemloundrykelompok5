<?php
namespace App\Models;

use App\Interfaces\CalculableInterface;

abstract class LaundryService implements CalculableInterface {
    protected int $id;
    protected string $serviceCode;
    protected string $serviceName;
    protected float $basePrice;

    public function __construct(string $serviceCode, string $serviceName, float $basePrice, int $id = 0) {
        $this->id = $id;
        $this->serviceCode = $serviceCode;
        $this->serviceName = $serviceName;
        $this->basePrice = $basePrice;
    }

    public function getId(): int { return $this->id; }
    public function getServiceCode(): string { return $this->serviceCode; }
    public function getServiceName(): string { return $this->serviceName; }
    public function getBasePrice(): float { return $this->basePrice; }

    public function getFormattedPrice(): string {
        return 'Rp ' . number_format($this->basePrice, 0, ',', '.');
    }

    abstract public function calculateTotal(float $quantity = 1.0): float;
    abstract public function getEstimatedHours(): int;
    abstract public function getRuleDescription(): string;
}
