<?php
namespace App\Models;

use App\Interfaces\IdentifiableInterface;

abstract class Person implements IdentifiableInterface {
    protected int $id;
    protected string $name;
    protected string $phone;
    protected string $address;

    public function __construct(string $name, string $phone, string $address, int $id = 0) {
        $this->id = $id;
        $this->name = trim($name);
        $this->phone = trim($phone);
        $this->address = trim($address);
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPhone(): string { return $this->phone; }
    public function getAddress(): string { return $this->address; }

    abstract public function getRole(): string;
    abstract public function getCode(): string;
}
