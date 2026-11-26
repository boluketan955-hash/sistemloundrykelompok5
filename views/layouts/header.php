<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$pageTitles = [
    'dashboard' => ['Dashboard Ringkasan', 'Pantau performa operasional, pendapatan, dan progres cucian secara real-time'],
    'services'  => ['Manajemen Layanan Laundry', 'Daftar paket layanan polimorfik (Kiloan, Satuan, dan Express)'],
    'customers' => ['Data Pelanggan Member', 'Kelola data pelanggan terdaftar dan akumulasi poin loyalitas'],
    'orders'    => ['Daftar Pesanan Laundry', 'Kelola seluruh transaksi cucian, status proses, dan pelunasan'],
    'create'    => ['Input Pesanan Baru', 'Buat transaksi laundry baru dengan kalkulasi tarif otomatis'],
    'edit'      => ['Update Status Pesanan', 'Perbarui tahapan proses cucian dan status pembayaran pelanggan']
];
$activeTitle = $pageTitles[$currentPage][0] ?? 'Sistem Manajemen Laundry';
$activeSubtitle = $pageTitles[$currentPage][1] ?? 'Aplikasi Berbasis PHP OOP — Kelompok 5 (Kelas 3C)';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($activeTitle) ?> — CleanWave Laundry OOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --primary-accent: #2563eb;
            --bg-canvas: #f1f5f9;
        }
        body {
            background-color: var(--bg-canvas);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: #f8fafc;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .sidebar-brand {
            padding: 1.4rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
        }
        .nav-menu {
            padding: 1.25rem 0.9rem;
            flex-grow: 1;
        }
        .nav-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.35rem;
            font-weight: 500;
            transition: all 0.18s ease;
        }
        .sidebar-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
        }
        .sidebar-link.active {
            background-color: var(--primary-accent);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .main-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .content-area {
            padding: 1.75rem 2rem;
            flex-grow: 1;
        }
        .card-clean {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem;
            height: 100%;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .table thead th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 2px solid #e2e8f0;
        }
        @media (max-width: 991.98px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: static; }
            .nav-menu { display: flex; flex-wrap: wrap; gap: 0.4rem; padding: 0.75rem; }
            .nav-label { display: none; }
            .sidebar-link { margin-bottom: 0; padding: 0.55rem 0.85rem; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-droplet-fill"></i></div>
        <div>
            <div class="fw-bold fs-5 lh-1 text-white">CleanWave</div>
            <small class="text-info" style="font-size: 0.75rem;">Laundry OOP System</small>
        </div>
    </div>

    <div class="nav-menu">
        <div class="nav-label">Menu Utama</div>
        <a href="index.php?page=dashboard" class="sidebar-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="index.php?page=services" class="sidebar-link <?= ($currentPage === 'services') ? 'active' : '' ?>">
            <i class="bi bi-tags-fill"></i>
            <span>Layanan</span>
        </a>
        <a href="index.php?page=customers" class="sidebar-link <?= ($currentPage === 'customers') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span>Pelanggan</span>
        </a>
        <a href="index.php?page=orders" class="sidebar-link <?= in_array($currentPage, ['orders', 'create', 'edit'], true) ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Pesanan</span>
        </a>
    </div>

    <div class="p-3 border-top border-secondary border-opacity-25 small text-secondary">
        <div class="fw-semibold text-light">Kelompok 5 — Kelas 3C</div>
        <div>Pemrograman Berorientasi Objek</div>
    </div>
</aside>

<div class="main-wrapper">
    <header class="topbar">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($activeTitle) ?></h4>
            <div class="text-muted small"><?= htmlspecialchars($activeSubtitle) ?></div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?page=create" class="btn btn-primary fw-semibold px-3">
                <i class="bi bi-plus-lg me-1"></i> Pesanan Baru
            </a>
        </div>
    </header>

    <main class="content-area">
        <?php if (!empty($_GET['msg'])): ?>
            <?php
            $alerts = [
                'created'         => ['success', 'Pesanan laundry baru berhasil ditambahkan dan dihitung secara otomatis!'],
                'updated'         => ['success', 'Status proses cucian dan pembayaran berhasil diperbarui!'],
                'deleted'         => ['warning', 'Data pesanan berhasil dihapus dari sistem.'],
                'customer_saved'  => ['success', 'Data pelanggan member baru berhasil disimpan!'],
                'customer_deleted'=> ['warning', 'Data pelanggan berhasil dihapus.'],
                'service_saved'   => ['success', 'Paket layanan laundry baru berhasil ditambahkan!'],
                'service_deleted' => ['warning', 'Paket layanan berhasil dihapus.'],
            ];
            $msgKey = $_GET['msg'];
            if (isset($alerts[$msgKey])):
            ?>
            <div class="alert alert-<?= $alerts[$msgKey][0] ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($alerts[$msgKey][1]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
        <?php endif; ?>
