<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="row g-4">
    <!-- Form Tambah Paket Layanan -->
    <div class="col-lg-4">
        <div class="card-clean p-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-plus-square-fill text-primary me-2"></i>Tambah Paket Layanan</h5>
            <p class="text-muted small mb-3">Setiap kategori memetakan ke subclass Polymorphic berbeda.</p>

            <form method="POST" action="index.php?page=store_service">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Layanan</label>
                    <input type="text" name="service_name" class="form-control" placeholder="Contoh: Cuci Setrika Wangi" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori Class OOP</label>
                    <select name="service_type" class="form-select" required>
                        <option value="kiloan">Kiloan (KiloanService — Min. 2 Kg)</option>
                        <option value="satuan">Satuan (SatuanService — +Rp 5.000/item)</option>
                        <option value="express">Express (ExpressService — Multiplier 1.25x)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga Dasar (Rp)</label>
                    <input type="number" name="base_price" min="1000" step="500" class="form-control" placeholder="7000" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Keterangan Tambahan</label>
                    <input type="text" name="extra_info" class="form-control" placeholder="Parfum, estimasi jam, dll.">
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="bi bi-save me-1"></i> Simpan Paket Layanan
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Layanan (Polymorphic Objects) -->
    <div class="col-lg-8">
        <div class="card-clean p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-tags-fill text-primary me-2"></i>Daftar Layanan Aktif</h5>
                <span class="badge bg-dark"><?= count($serviceObjects) ?> Paket Tersedia</span>
            </div>

            <div class="row g-3">
                <?php foreach ($serviceObjects as $srv): ?>
                <?php
                    $className = (new \ReflectionClass($srv))->getShortName();
                    $badgeColor = match($className) {
                        'KiloanService'  => 'bg-primary',
                        'SatuanService'  => 'bg-info text-dark',
                        'ExpressService' => 'bg-warning text-dark',
                        default          => 'bg-secondary'
                    };
                ?>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge <?= $badgeColor ?>"><?= htmlspecialchars($className) ?></span>
                                <span class="text-muted small fw-semibold"><?= htmlspecialchars($srv->getServiceCode()) ?></span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($srv->getServiceName()) ?></h6>
                            <div class="fs-5 fw-bold text-primary mb-2"><?= htmlspecialchars($srv->getFormattedPrice()) ?> <small class="text-muted fs-6 fw-normal">/ unit</small></div>
                            <div class="small text-muted mb-1"><i class="bi bi-clock me-1"></i> Estimasi Selesai: <strong><?= $srv->getEstimatedHours() ?> Jam</strong></div>
                            <div class="small text-secondary"><i class="bi bi-info-circle me-1"></i> <?= htmlspecialchars($srv->getRuleDescription()) ?></div>
                        </div>
                        <div class="mt-3 pt-2 border-top text-end">
                            <a href="index.php?page=delete_service&id=<?= $srv->getId() ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Hapus paket layanan ini?')">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
