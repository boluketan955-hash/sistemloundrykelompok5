<?php require __DIR__ . '/../layouts/header.php'; ?>

<!-- 4 KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Total Pesanan</div>
                <div class="fs-3 fw-bold text-dark mt-1"><?= (int)$stats['total_orders'] ?></div>
                <div class="small text-primary mt-1"><?= (int)$stats['active_orders'] ?> sedang diproses</div>
            </div>
            <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-basket2-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Pendapatan Lunas</div>
                <div class="fs-4 fw-bold text-success mt-1">Rp <?= number_format($stats['paid_revenue'], 0, ',', '.') ?></div>
                <div class="small text-muted mt-1">Belum Lunas: Rp <?= number_format($stats['unpaid_revenue'], 0, ',', '.') ?></div>
            </div>
            <div class="kpi-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Pelanggan Member</div>
                <div class="fs-3 fw-bold text-dark mt-1"><?= (int)$stats['total_customers'] ?></div>
                <div class="small text-info mt-1">Terdaftar aktif</div>
            </div>
            <div class="kpi-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small fw-semibold">Paket Layanan</div>
                <div class="fs-3 fw-bold text-dark mt-1"><?= (int)$stats['total_services'] ?></div>
                <div class="small text-warning mt-1">Kiloan • Satuan • Express</div>
            </div>
            <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-tags-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Row Tengah: Progres Tahapan Cucian & Akses Cepat -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-clean p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-bar-chart-steps text-primary me-2"></i>Distribusi Status Cucian</h5>
            <?php
            $total = max(1, (int)$stats['total_orders']);
            $statusMeta = [
                'Diterima'     => ['secondary', 'bi-inbox-fill'],
                'Dicuci'       => ['primary',   'bi-water'],
                'Disetrika'    => ['info',      'bi-wind'],
                'Siap Diambil' => ['warning',   'bi-bag-check-fill'],
                'Selesai'      => ['success',   'bi-check-circle-fill'],
            ];
            foreach ($statusMeta as $stName => [$color, $icon]):
                $cnt = $stats['status_counts'][$stName] ?? 0;
                $pct = round(($cnt / $total) * 100);
            ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-semibold small"><i class="bi <?= $icon ?> text-<?= $color ?> me-1"></i> <?= $stName ?></span>
                    <span class="badge bg-<?= $color ?> <?= in_array($color, ['info', 'warning']) ? 'text-dark' : '' ?>"><?= $cnt ?> Pesanan (<?= $pct ?>%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-clean p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Navigasi Cepat Operasional</h5>
                <p class="text-muted small">Kelola transaksi dan master data melalui empat modul utama berbasis PHP OOP:</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=create" class="btn btn-outline-primary text-start fw-semibold py-2">
                        <i class="bi bi-plus-circle-fill me-2"></i> 1. Input Transaksi Pesanan Baru
                    </a>
                    <a href="index.php?page=orders" class="btn btn-outline-dark text-start fw-semibold py-2">
                        <i class="bi bi-receipt me-2"></i> 2. Update Status Cucian & Pembayaran
                    </a>
                    <a href="index.php?page=services" class="btn btn-outline-info text-dark text-start fw-semibold py-2">
                        <i class="bi bi-tags me-2"></i> 3. Lihat & Tambah Paket Layanan
                    </a>
                    <a href="index.php?page=customers" class="btn btn-outline-success text-start fw-semibold py-2">
                        <i class="bi bi-person-plus-fill me-2"></i> 4. Registrasi Pelanggan Member Baru
                    </a>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top small text-muted">
                Arsitektur: <strong>MVC PHP 8 OOP</strong> • Database: <strong>MySQL PDO</strong>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pesanan Terbaru -->
<div class="card-clean p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">5 Pesanan Laundry Terbaru</h5>
        <a href="index.php?page=orders" class="btn btn-sm btn-primary">Lihat Semua Pesanan &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Pelanggan</th>
                    <th>Paket Layanan</th>
                    <th>Total Tagihan</th>
                    <th>Status Cucian</th>
                    <th>Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data transaksi pesanan.</td></tr>
                <?php else: ?>
                    <?php foreach (array_slice($recentOrders, 0, 5) as $row): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($row['order_code']) ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($row['customer_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($row['member_code']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['service_name'] ?? '-') ?></td>
                        <td class="fw-bold">Rp <?= number_format((float)$row['total_amount'], 0, ',', '.') ?></td>
                        <td>
                            <?php
                            $badge = match($row['status']) {
                                'Diterima'     => 'bg-secondary',
                                'Dicuci'       => 'bg-primary',
                                'Disetrika'    => 'bg-info text-dark',
                                'Siap Diambil' => 'bg-warning text-dark',
                                'Selesai'      => 'bg-success',
                                default        => 'bg-dark'
                            };
                            ?>
                            <span class="badge <?= $badge ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td>
                            <span class="badge <?= ($row['payment_status'] === 'Lunas') ? 'bg-success' : 'bg-danger' ?>">
                                <?= htmlspecialchars($row['payment_status'] ?? 'Belum Lunas') ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
