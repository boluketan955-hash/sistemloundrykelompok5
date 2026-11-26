<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-clean p-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-person-plus-fill text-primary me-2"></i>Registrasi Member Baru</h5>
            <p class="text-muted small mb-3">Pelanggan member mendapatkan +10 poin loyalitas setiap transaksi.</p>

            <form method="POST" action="index.php?page=store_customer">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama pelanggan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nomor WhatsApp / HP</label>
                    <input type="text" name="phone" class="form-control" placeholder="0812xxxxxxxx" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Alamat domisili pelanggan"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="bi bi-save me-1"></i> Simpan Data Pelanggan
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-clean p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Pelanggan Terdaftar</h5>
                <span class="badge bg-primary"><?= count($customers) ?> Member</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode Member</th>
                            <th>Nama Pelanggan</th>
                            <th>Kontak WA/HP</th>
                            <th>Alamat</th>
                            <th>Poin</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data pelanggan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($customers as $c): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($c['member_code']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($c['name']) ?></td>
                                <td><i class="bi bi-whatsapp text-success me-1"></i><?= htmlspecialchars($c['phone']) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($c['address']) ?></td>
                                <td><span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i><?= (int)$c['loyalty_points'] ?> pts</span></td>
                                <td class="text-end">
                                    <a href="index.php?page=delete_customer&id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Hapus data pelanggan ini?')">
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
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
