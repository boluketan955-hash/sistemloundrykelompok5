<?php
namespace App\Interfaces;

interface IdentifiableInterface {
    public function getId(): int;
    public function getCode(): string;
}
