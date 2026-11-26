<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="card-clean p-4 mx-auto" style="max-width: 760px;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0">Form Transaksi Pesanan Baru</h5>
            <small class="text-muted">Perhitungan biaya otomatis menerapkan aturan Polymorphism tiap jenis layanan</small>
        </div>
        <a href="index.php?page=orders" class="btn btn-sm btn-outline-secondary">&larr; Kembali</a>
    </div>

    <form method="POST" action="index.php?page=store">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Pelanggan Member</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['member_code'] . ' - ' . $c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Petugas Penerima (Staff)</label>
                <select name="staff_id" class="form-select" required>
                    <?php foreach ($staffList as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['staff_code'] . ' - ' . $s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Pilih Paket Layanan</label>
                <select name="service_id" id="serviceSelect" class="form-select" required>
                    <?php foreach ($services as $srv): ?>
                        <option value="<?= $srv['id'] ?>"
                                data-type="<?= htmlspecialchars($srv['service_type']) ?>"
                                data-price="<?= (float)$srv['base_price'] ?>">
                            <?= htmlspecialchars($srv['service_name']) ?> — Rp <?= number_format((float)$srv['base_price'], 0, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Berat (Kg) / Jumlah (Pcs)</label>
                <input type="number" step="0.5" min="0.5" name="quantity" id="qtyInput" class="form-control" value="2.0" required>
            </div>
        </div>

        <!-- Live Preview Box Kalkulasi Polymorphic -->
        <div class="alert alert-primary d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="small fw-bold text-uppercase">Estimasi Tagihan Otomatis (Polymorphic Preview)</div>
                <div class="small" id="rulePreview">Aturan Kiloan: Minimal berat 2.0 Kg</div>
            </div>
            <div class="fs-4 fw-bold" id="totalPreview">Rp 14.000</div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Metode Pembayaran</label>
                <select name="payment_method" class="form-select">
                    <option value="Tunai">Tunai</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status Pembayaran</label>
                <select name="payment_status" class="form-select">
                    <option value="Belum Lunas">Belum Lunas</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Catatan Khusus Cucian</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Pisahkan pakaian putih, lipat rapi..."></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="index.php?page=orders" class="btn btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-circle me-1"></i> Simpan Transaksi
            </button>
        </div>
    </form>
</div>

<script>
function updateEstimate() {
    const sel = document.getElementById('serviceSelect');
    const opt = sel.options[sel.selectedIndex];
    const type = opt.getAttribute('data-type');
    const price = parseFloat(opt.getAttribute('data-price')) || 0;
    const qty = parseFloat(document.getElementById('qtyInput').value) || 0;

    let total = 0;
    let rule = '';
    if (type === 'kiloan') {
        const eff = Math.max(2.0, qty);
        total = eff * price;
        rule = `KiloanService: Berlaku batas minimal 2.0 Kg (Dihitung ${eff} Kg)`;
    } else if (type === 'satuan') {
        total = qty * (price + 5000);
        rule = `SatuanService: Harga dasar + biaya perawatan khusus Rp 5.000/item`;
    } else if (type === 'express') {
        total = qty * price * 1.25;
        rule = `ExpressService: Tarif prioritas 6 jam (Multiplier 1.25x)`;
    }

    document.getElementById('rulePreview').textContent = rule;
    document.getElementById('totalPreview').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
}
document.getElementById('serviceSelect').addEventListener('change', updateEstimate);
document.getElementById('qtyInput').addEventListener('input', updateEstimate);
updateEstimate();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
