<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $parts = explode('\\', $relative);
    $folder = strtolower(array_shift($parts));
    $file = __DIR__ . '/' . $folder . '/' . implode('/', $parts) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Controllers\OrderController;
use App\Controllers\CustomerController;
use App\Controllers\ServiceController;
use App\Exceptions\LaundryException;

$page = $_GET['page'] ?? 'dashboard';

try {
    $orderCtrl = new OrderController();
    $custCtrl  = new CustomerController();
    $srvCtrl   = new ServiceController();

    switch ($page) {
        // 1. DASHBOARD
        case 'dashboard':
            $stats = $orderCtrl->getDashboardStats();
            $recentOrders = $orderCtrl->index();
            require __DIR__ . '/views/dashboard/index.php';
            break;

        // 2. LAYANAN
        case 'services':
            $serviceObjects = $srvCtrl->getAllObjects();
            require __DIR__ . '/views/services/index.php';
            break;

        case 'store_service':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $srvCtrl->create($_POST);
                header('Location: index.php?page=services&msg=service_saved');
                exit;
            }
            break;

        case 'delete_service':
            $id = (int)($_GET['id'] ?? 0);
            $srvCtrl->delete($id);
            header('Location: index.php?page=services&msg=service_deleted');
            exit;

        // 3. PELANGGAN
        case 'customers':
            $customers = $custCtrl->getAll();
            require __DIR__ . '/views/customers/index.php';
            break;

        case 'store_customer':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $custCtrl->create($_POST);
                header('Location: index.php?page=customers&msg=customer_saved');
                exit;
            }
            break;

        case 'delete_customer':
            $id = (int)($_GET['id'] ?? 0);
            $custCtrl->delete($id);
            header('Location: index.php?page=customers&msg=customer_deleted');
            exit;

        // 4. PESANAN (CRUD UTAMA)
        case 'orders':
            $statusFilter = $_GET['status'] ?? null;
            $orders = $orderCtrl->index($statusFilter);
            require __DIR__ . '/views/orders/index.php';
            break;

        case 'create':
            $customers = $orderCtrl->getCustomers();
            $staffList = $orderCtrl->getStaffList();
            $services  = $orderCtrl->getServices();
            require __DIR__ . '/views/orders/create.php';
            break;

        case 'store':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $orderCtrl->store($_POST);
                header('Location: index.php?page=orders&msg=created');
                exit;
            }
            break;

        case 'edit':
            $id = (int)($_GET['id'] ?? 0);
            $order = $orderCtrl->show($id);
            if (!$order) {
                throw new LaundryException("Data pesanan ID #{$id} tidak ditemukan.", "ERR_NOT_FOUND");
            }
            require __DIR__ . '/views/orders/edit.php';
            break;

        case 'update':
            $id = (int)($_GET['id'] ?? 0);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $orderCtrl->update($id, $_POST);
                header('Location: index.php?page=orders&msg=updated');
                exit;
            }
            break;

        case 'delete':
            $id = (int)($_GET['id'] ?? 0);
            $orderCtrl->delete($id);
            header('Location: index.php?page=orders&msg=deleted');
            exit;

        default:
            header('Location: index.php?page=dashboard');
            exit;
    }
} catch (LaundryException $e) {
    echo "<div style='font-family:system-ui,sans-serif;max-width:560px;margin:60px auto;padding:24px;border:1px solid #fca5a5;background:#fef2f2;border-radius:12px;color:#991b1b;'>";
    echo "<h4 style='margin-top:0;'>Validasi / Kesalahan Aplikasi</h4>";
    echo "<p>" . htmlspecialchars($e->getFormattedMessage()) . "</p>";
    echo "<a href='javascript:history.back()' style='display:inline-block;padding:8px 16px;background:#991b1b;color:#fff;text-decoration:none;border-radius:8px;'>&larr; Kembali</a></div>";
} catch (\Throwable $e) {
    echo "<div style='font-family:system-ui,sans-serif;max-width:560px;margin:60px auto;padding:24px;border:1px solid #fca5a5;background:#fef2f2;border-radius:12px;color:#991b1b;'>";
    echo "<h4 style='margin-top:0;'>System Error</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='index.php?page=dashboard' style='display:inline-block;padding:8px 16px;background:#991b1b;color:#fff;text-decoration:none;border-radius:8px;'>&larr; Ke Dashboard</a></div>";
}
