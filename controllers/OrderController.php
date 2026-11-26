<?php
namespace App\Controllers;

use PDO;
use App\Config\Database;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\OrderItem;
use App\Models\Order;
use App\Exceptions\InvalidOrderException;

class OrderController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index(?string $statusFilter = null): array {
        $sql = "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone, c.member_code,
                       s.name AS staff_name, p.payment_status, p.payment_method,
                       srv.service_name, srv.service_type, oi.quantity
                FROM orders o
                JOIN customers c ON o.customer_id = c.id
                JOIN staff s ON o.staff_id = s.id
                LEFT JOIN payments p ON p.order_id = o.id
                LEFT JOIN order_items oi ON oi.order_id = o.id
                LEFT JOIN services srv ON oi.service_id = srv.id";
        $params = [];
        if (!empty($statusFilter) && in_array($statusFilter, Order::VALID_STATUSES, true)) {
            $sql .= " WHERE o.status = :status";
            $params[':status'] = $statusFilter;
        }
        $sql .= " ORDER BY o.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDashboardStats(): array {
        $totalOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $paidRevenue = (float)$this->db->query("SELECT COALESCE(SUM(amount_paid), 0) FROM payments WHERE payment_status = 'Lunas'")->fetchColumn();
        $unpaidRevenue = (float)$this->db->query("SELECT COALESCE(SUM(o.total_amount), 0) FROM orders o JOIN payments p ON p.order_id = o.id WHERE p.payment_status = 'Belum Lunas'")->fetchColumn();
        $activeOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE status != 'Selesai'")->fetchColumn();
        $totalCustomers = (int)$this->db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        $totalServices = (int)$this->db->query("SELECT COUNT(*) FROM services")->fetchColumn();

        $statusCounts = [];
        foreach (Order::VALID_STATUSES as $st) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE status = :st");
            $stmt->execute([':st' => $st]);
            $statusCounts[$st] = (int)$stmt->fetchColumn();
        }

        return [
            'total_orders'    => $totalOrders,
            'paid_revenue'    => $paidRevenue,
            'unpaid_revenue'  => $unpaidRevenue,
            'active_orders'   => $activeOrders,
            'total_customers' => $totalCustomers,
            'total_services'  => $totalServices,
            'status_counts'   => $statusCounts
        ];
    }

    public function store(array $data): int {
        if (empty($data['customer_id']) || empty($data['staff_id']) || empty($data['service_id'])) {
            throw new InvalidOrderException('form', 'Pelanggan, Petugas, dan Jenis Layanan wajib dipilih.');
        }
        $qty = (float)($data['quantity'] ?? 0);
        if ($qty <= 0) {
            throw new InvalidOrderException('quantity', 'Berat / jumlah item laundry harus lebih besar dari 0.');
        }

        $stmtC = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
        $stmtC->execute([':id' => $data['customer_id']]);
        $cRow = $stmtC->fetch();
        if (!$cRow) throw new InvalidOrderException('customer_id', 'Data pelanggan tidak ditemukan.');

        $stmtS = $this->db->prepare("SELECT * FROM staff WHERE id = :id");
        $stmtS->execute([':id' => $data['staff_id']]);
        $sRow = $stmtS->fetch();
        if (!$sRow) throw new InvalidOrderException('staff_id', 'Data petugas tidak ditemukan.');

        $stmtSrv = $this->db->prepare("SELECT * FROM services WHERE id = :id");
        $stmtSrv->execute([':id' => $data['service_id']]);
        $srvRow = $stmtSrv->fetch();
        if (!$srvRow) throw new InvalidOrderException('service_id', 'Layanan laundry tidak ditemukan.');

        $serviceCtrl = new ServiceController();
        $customer = new Customer($cRow['member_code'], $cRow['name'], $cRow['phone'], $cRow['address'], (int)$cRow['loyalty_points'], (int)$cRow['id']);
        $staff = new Staff($sRow['staff_code'], $sRow['name'], $sRow['phone'], $sRow['address'], $sRow['position'], $sRow['shift'], (int)$sRow['id']);
        $serviceObj = $serviceCtrl->instantiateService($srvRow);

        $orderCode = 'ORD-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);
        $order = new Order($orderCode, $customer, $staff, 'Diterima', $data['notes'] ?? '');
        $orderItem = new OrderItem($serviceObj, $qty, $data['item_notes'] ?? '');
        $order->addItem($orderItem);

        $this->db->beginTransaction();
        try {
            $stmtOrd = $this->db->prepare(
                "INSERT INTO orders (order_code, customer_id, staff_id, status, total_amount, notes)
                 VALUES (:code, :cid, :sid, :status, :total, :notes)"
            );
            $stmtOrd->execute([
                ':code'   => $order->getCode(),
                ':cid'    => $customer->getId(),
                ':sid'    => $staff->getId(),
                ':status' => $order->getStatus(),
                ':total'  => $order->getTotalAmount(),
                ':notes'  => $order->getNotes()
            ]);
            $orderId = (int)$this->db->lastInsertId();

            $stmtItem = $this->db->prepare(
                "INSERT INTO order_items (order_id, service_id, quantity, subtotal, item_notes)
                 VALUES (:oid, :srvid, :qty, :sub, :notes)"
            );
            $stmtItem->execute([
                ':oid'   => $orderId,
                ':srvid' => $serviceObj->getId(),
                ':qty'   => $orderItem->getQuantity(),
                ':sub'   => $orderItem->getSubtotal(),
                ':notes' => $orderItem->getNotes()
            ]);

            $payStatus = ($data['payment_status'] ?? 'Belum Lunas') === 'Lunas' ? 'Lunas' : 'Belum Lunas';
            $paidAmt = $payStatus === 'Lunas' ? $order->getTotalAmount() : 0.0;
            $payCode = 'PAY-' . date('Ymd') . '-' . str_pad((string)$orderId, 3, '0', STR_PAD_LEFT);

            $stmtPay = $this->db->prepare(
                "INSERT INTO payments (payment_code, order_id, amount_paid, payment_method, payment_status, paid_at)
                 VALUES (:pcode, :oid, :amt, :method, :pstatus, :paid_at)"
            );
            $stmtPay->execute([
                ':pcode'   => $payCode,
                ':oid'     => $orderId,
                ':amt'     => $paidAmt,
                ':method'  => $data['payment_method'] ?? 'Tunai',
                ':pstatus' => $payStatus,
                ':paid_at' => $payStatus === 'Lunas' ? date('Y-m-d H:i:s') : null
            ]);

            $stmtPts = $this->db->prepare("UPDATE customers SET loyalty_points = loyalty_points + 10 WHERE id = :cid");
            $stmtPts->execute([':cid' => $customer->getId()]);

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function show(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT o.*, c.name AS customer_name, c.member_code, p.payment_status, p.payment_method,
                    srv.service_name, srv.service_type, oi.quantity, oi.subtotal
             FROM orders o
             JOIN customers c ON o.customer_id = c.id
             LEFT JOIN payments p ON p.order_id = o.id
             LEFT JOIN order_items oi ON oi.order_id = o.id
             LEFT JOIN services srv ON oi.service_id = srv.id
             WHERE o.id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(int $id, array $data): bool {
        $status = $data['status'] ?? '';
        if (!in_array($status, Order::VALID_STATUSES, true)) {
            throw new InvalidOrderException('status', "Status '{$status}' tidak valid.");
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("UPDATE orders SET status = :status, notes = :notes WHERE id = :id");
            $stmt->execute([
                ':status' => $status,
                ':notes'  => $data['notes'] ?? '',
                ':id'     => $id
            ]);

            if (!empty($data['payment_status'])) {
                $stmtGet = $this->db->prepare("SELECT total_amount FROM orders WHERE id = :id");
                $stmtGet->execute([':id' => $id]);
                $total = (float)$stmtGet->fetchColumn();

                $pStatus = $data['payment_status'] === 'Lunas' ? 'Lunas' : 'Belum Lunas';
                $stmtPay = $this->db->prepare(
                    "UPDATE payments SET payment_status = :ps, payment_method = :pm,
                     amount_paid = :amt, paid_at = :paid WHERE order_id = :oid"
                );
                $stmtPay->execute([
                    ':ps'   => $pStatus,
                    ':pm'   => $data['payment_method'] ?? 'Tunai',
                    ':amt'  => $pStatus === 'Lunas' ? $total : 0.0,
                    ':paid' => $pStatus === 'Lunas' ? date('Y-m-d H:i:s') : null,
                    ':oid'  => $id
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM orders WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getCustomers(): array {
        return $this->db->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();
    }

    public function getStaffList(): array {
        return $this->db->query("SELECT * FROM staff ORDER BY name ASC")->fetchAll();
    }

    public function getServices(): array {
        return $this->db->query("SELECT * FROM services ORDER BY id ASC")->fetchAll();
    }
}
