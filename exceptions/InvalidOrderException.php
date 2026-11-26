<?php
namespace App\Exceptions;

class InvalidOrderException extends LaundryException {
    private string $fieldName;

    public function __construct(string $fieldName, string $message) {
        parent::__construct($message, 'ERR_INVALID_INPUT', ['field' => $fieldName], 422);
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string {
        return $this->fieldName;
    }
}
