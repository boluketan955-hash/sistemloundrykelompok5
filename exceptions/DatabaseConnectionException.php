<?php
namespace App\Exceptions;

class DatabaseConnectionException extends LaundryException {
    private string $sqlState;

    public function __construct(string $message, string $sqlState = 'HY000') {
        parent::__construct($message, 'ERR_DB_CONNECTION', ['sql_state' => $sqlState], 500);
        $this->sqlState = $sqlState;
    }

    public function getSqlState(): string {
        return $this->sqlState;
    }
}
