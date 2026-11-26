<?php
namespace App\Models;

use App\Interfaces\NotifiableInterface;

class Customer extends Person implements NotifiableInterface {
    private string $memberCode;
    private int $loyaltyPoints;

    public function __construct(
        string $memberCode,
        string $name,
        string $phone,
        string $address,
        int $loyaltyPoints = 0,
        int $id = 0
    ) {
        parent::__construct($name, $phone, $address, $id);
        $this->memberCode = $memberCode;
        $this->loyaltyPoints = $loyaltyPoints;
    }

    public function getCode(): string { return $this->memberCode; }
    public function getRole(): string { return 'Pelanggan Member'; }
    public function getLoyaltyPoints(): int { return $this->loyaltyPoints; }

    public function addPoints(int $points): void {
        if ($points > 0) {
            $this->loyaltyPoints += $points;
        }
    }

    public function sendNotification(string $message): bool {
        return !empty($this->phone) && !empty($message);
    }

    public function getNotificationTarget(): string {
        return $this->phone;
    }
}
