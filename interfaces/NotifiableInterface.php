<?php
namespace App\Interfaces;

interface NotifiableInterface {
    public function sendNotification(string $message): bool;
    public function getNotificationTarget(): string;
}
