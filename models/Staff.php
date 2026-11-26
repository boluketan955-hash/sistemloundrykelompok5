<?php
namespace App\Models;

class Staff extends Person {
    private string $staffCode;
    private string $position;
    private string $shift;

    public function __construct(
        string $staffCode,
        string $name,
        string $phone,
        string $address,
        string $position,
        string $shift,
        int $id = 0
    ) {
        parent::__construct($name, $phone, $address, $id);
        $this->staffCode = $staffCode;
        $this->position = $position;
        $this->shift = $shift;
    }

    public function getCode(): string { return $this->staffCode; }
    public function getRole(): string { return 'Petugas Laundry (' . $this->position . ')'; }
    public function getPosition(): string { return $this->position; }
    public function getShift(): string { return $this->shift; }
}
