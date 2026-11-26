<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="card-clean p-4 mx-auto" style="max-width: 680px;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0">Update Pesanan: <span class="text-primary"><?= htmlspecialchars($order['order_code']) ?></span></h5>
            <small class="text-muted">Pelanggan: <strong><?= htmlspecialchars($order['customer_name']) ?></strong> (<?= htmlspecialchars($order['member_code']) ?>)</small>
        </div>
        <a href="index.php?page=orders" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
    </div>

    <div class="bg-light rounded-3 p-3 mb-3 border d-flex justify-content-between align-items-center">
        <div>
            <div class="small text-muted">Layanan & Jumlah</div>
            <div class="fw-semibold"><?= htmlspecialchars($order['service_name'] ?? '-') ?> (<?= number_format((float)$order['quantity'], 1) ?>)</div>
        </div>
        <div class="text-end">
            <div class="small text-muted">Total Tagihan</div>
            <div class="fs-5 fw-bold text-primary">Rp <?= number_format((float)$order['total_amount'], 0, ',', '.') ?></div>
        </div>
    </div>

    <form method="POST" action="index.php?page=update&id=<?= (int)$order['id'] ?>">
        <div class="mb-3">
            <label class="form-label fw-semibold">Status Proses Cucian</label>
            <select name="status" class="form-select">
                <?php foreach (['Diterima', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai'] as $st): ?>
                    <option value="<?= $st ?>" <?= ($order['status'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status Pembayaran</label>
                <select name="payment_status" class="form-select">
                    <option value="Belum Lunas" <?= (($order['payment_status'] ?? '') === 'Belum Lunas') ? 'selected' : '' ?>>Belum Lunas</option>
                    <option value="Lunas" <?= (($order['payment_status'] ?? '') === 'Lunas') ? 'selected' : '' ?>>Lunas</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Metode Pembayaran</label>
                <select name="payment_method" class="form-select">
                    <?php foreach (['Tunai', 'QRIS', 'Transfer Bank'] as $pm): ?>
                        <option value="<?= $pm ?>" <?= (($order['payment_method'] ?? '') === $pm) ? 'selected' : '' ?>><?= $pm ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Catatan Pesanan</label>
            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="index.php?page=orders" class="btn btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
