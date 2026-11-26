<?php
namespace App\Exceptions;

use Exception;

class LaundryException extends Exception {
    protected string $errorCode;
    protected array $context;

    public function __construct(string $message, string $errorCode = 'ERR_LAUNDRY', array $context = [], int $code = 0) {
        parent::__construct($message, $code);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function getErrorCode(): string {
        return $this->errorCode;
    }

    public function getFormattedMessage(): string {
        return "[{$this->errorCode}] {$this->getMessage()}";
    }
}
