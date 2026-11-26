# CleanWave — Sistem Informasi Manajemen Laundry (PHP OOP)

**Kelompok 5 — Kelas 3C**
- Afdal Novana Hendra (2571020079)
- Labib Rif'at Besya (2571020066)
- Risa Wulandari (2571020076)
- Hengki Racheli Pratama (2571020065)

## Struktur Menu Web Utama
1. **Dashboard (`?page=dashboard`)**: Ringkasan KPI (Total Pesanan, Pendapatan Lunas, Pelanggan Member, Paket Layanan), grafik distribusi status cucian, dan 5 transaksi terbaru.
2. **Layanan (`?page=services`)**: Menampilkan dan menambah paket layanan berbasis Polymorphism (`KiloanService`, `SatuanService`, `ExpressService`) beserta estimasi waktu pengerjaan.
3. **Pelanggan (`?page=customers`)**: Registrasi member baru dan tabel daftar pelanggan lengkap dengan poin loyalitas.
4. **Pesanan (`?page=orders`)**: Manajemen CRUD pesanan laundry dengan filter status (`Diterima`, `Dicuci`, `Disetrika`, `Siap Diambil`, `Selesai`) dan live preview kalkulasi tarif.

## Cara Instalasi
1. Ekstrak folder `sistem-laundry-oop-v2` ke `C:\laragon\www\` atau `C:\xampp\htdocs\`.
2. Import `database/db_laundry_oop.sql` ke MySQL / phpMyAdmin.
3. Buka `http://localhost/sistem-laundry-oop-v2/` di browser.
