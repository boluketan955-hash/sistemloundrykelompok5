<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$activeStatus = $_GET['status'] ?? '';
$filterTabs = ['', 'Diterima', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai'];
?>

<div class="card-clean p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <!-- Filter Pills -->
        <div class="d-flex flex-wrap gap-1">
            <?php foreach ($filterTabs as $tab): ?>
                <?php $label = $tab === '' ? 'Semua Pesanan' : $tab; ?>
                <a href="index.php?page=orders<?= $tab !== '' ? '&status=' . urlencode($tab) : '' ?>"
                   class="btn btn-sm <?= ($activeStatus === $tab) ? 'btn-dark fw-semibold' : 'btn-outline-secondary' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <a href="index.php?page=create" class="btn btn-primary btn-sm fw-semibold px-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pesanan
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Pelanggan</th>
                    <th>Layanan & Kategori</th>
                    <th>Qty</th>
                    <th>Total Biaya</th>
                    <th>Status Cucian</th>
                    <th>Pembayaran</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data pesanan untuk filter ini.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $row): ?>
                <tr>
                    <td>
                        <div class="fw-bold text-primary"><?= htmlspecialchars($row['order_code']) ?></div>
                        <small class="text-muted">Kasir: <?= htmlspecialchars($row['staff_name']) ?></small>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($row['customer_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($row['member_code']) ?> • <?= htmlspecialchars($row['customer_phone']) ?></small>
                    </td>
                    <td>
                        <div class="fw-medium"><?= htmlspecialchars($row['service_name'] ?? '-') ?></div>
                        <span class="badge bg-light text-dark border"><?= strtoupper(htmlspecialchars($row['service_type'] ?? '')) ?></span>
                    </td>
                    <td class="fw-semibold"><?= number_format((float)$row['quantity'], 1) ?></td>
                    <td class="fw-bold text-dark">Rp <?= number_format((float)$row['total_amount'], 0, ',', '.') ?></td>
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
                        <span class="badge <?= $badge ?> px-2 py-1"><?= htmlspecialchars($row['status']) ?></span>
                    </td>
                    <td>
                        <span class="badge <?= ($row['payment_status'] === 'Lunas') ? 'bg-success' : 'bg-danger' ?>">
                            <?= htmlspecialchars($row['payment_status'] ?? 'Belum Lunas') ?>
                        </span>
                        <div class="small text-muted"><?= htmlspecialchars($row['payment_method'] ?? '-') ?></div>
                    </td>
                    <td class="text-end">
                        <a href="index.php?page=edit&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square"></i> Update
                        </a>
                        <a href="index.php?page=delete&id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
