<?php
namespace App\Controllers;

use PDO;
use App\Config\Database;
use App\Models\Customer;
use App\Exceptions\InvalidOrderException;

class CustomerController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->prepare("SELECT * FROM customers ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        if (empty(trim($data['name'] ?? '')) || empty(trim($data['phone'] ?? ''))) {
            throw new InvalidOrderException('customer', 'Nama lengkap dan nomor WhatsApp/HP pelanggan wajib diisi.');
        }
        $code = 'MBR-' . str_pad((string)random_int(10, 999), 3, '0', STR_PAD_LEFT);
        $customer = new Customer($code, $data['name'], $data['phone'], $data['address'] ?? '-');

        $stmt = $this->db->prepare(
            "INSERT INTO customers (member_code, name, phone, address, loyalty_points)
             VALUES (:code, :name, :phone, :addr, 0)"
        );
        $stmt->execute([
            ':code'  => $customer->getCode(),
            ':name'  => $customer->getName(),
            ':phone' => $customer->getPhone(),
            ':addr'  => $customer->getAddress()
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool {
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = :id");
        $stmtCheck->execute([':id' => $id]);
        if ((int)$stmtCheck->fetchColumn() > 0) {
            throw new InvalidOrderException('customer_id', 'Pelanggan tidak dapat dihapus karena memiliki riwayat pesanan aktif.');
        }
        $stmt = $this->db->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
