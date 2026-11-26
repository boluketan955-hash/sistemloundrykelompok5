<?php
namespace App\Controllers;

use PDO;
use App\Config\Database;
use App\Models\LaundryService;
use App\Models\KiloanService;
use App\Models\SatuanService;
use App\Models\ExpressService;
use App\Exceptions\InvalidOrderException;

class ServiceController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Mengembalikan array objek Polymorphic LaundryService
    public function getAllObjects(): array {
        $stmt = $this->db->prepare("SELECT * FROM services ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $objects = [];
        foreach ($rows as $row) {
            $objects[] = $this->instantiateService($row);
        }
        return $objects;
    }

    public function instantiateService(array $row): LaundryService {
        return match ($row['service_type']) {
            'kiloan'  => new KiloanService($row['service_code'], $row['service_name'], (float)$row['base_price'], 'Lavender', (int)$row['id']),
            'satuan'  => new SatuanService($row['service_code'], $row['service_name'], (float)$row['base_price'], 'Pakaian Khusus', true, (int)$row['id']),
            'express' => new ExpressService($row['service_code'], $row['service_name'], (float)$row['base_price'], 1.25, 1, (int)$row['id']),
            default   => throw new InvalidOrderException('service_type', 'Tipe layanan tidak dikenali.')
        };
    }

    public function create(array $data): int {
        $name = trim($data['service_name'] ?? '');
        $type = $data['service_type'] ?? '';
        $price = (float)($data['base_price'] ?? 0);

        if (empty($name) || !in_array($type, ['kiloan', 'satuan', 'express'], true) || $price <= 0) {
            throw new InvalidOrderException('service', 'Nama layanan, kategori valid, dan harga dasar (> 0) wajib diisi.');
        }

        $prefix = match ($type) {
            'kiloan' => 'SRV-K',
            'satuan' => 'SRV-S',
            'express' => 'SRV-E',
        };
        $code = $prefix . str_pad((string)random_int(10, 99), 2, '0', STR_PAD_LEFT);

        $stmt = $this->db->prepare(
            "INSERT INTO services (service_code, service_name, service_type, base_price, extra_info)
             VALUES (:code, :name, :type, :price, :info)"
        );
        $stmt->execute([
            ':code'  => $code,
            ':name'  => $name,
            ':type'  => $type,
            ':price' => $price,
            ':info'  => trim($data['extra_info'] ?? 'Standar Operasional Laundry')
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id): bool {
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM order_items WHERE service_id = :id");
        $stmtCheck->execute([':id' => $id]);
        if ((int)$stmtCheck->fetchColumn() > 0) {
            throw new InvalidOrderException('service_id', 'Layanan tidak dapat dihapus karena sudah digunakan pada riwayat transaksi pesanan.');
        }
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
