-- Database: db_laundry_oop
-- Kelompok 5 (Kelas 3C) - Sistem Informasi Manajemen Laundry Menggunakan Pemrograman Berorientasi Objek

CREATE DATABASE IF NOT EXISTS db_laundry_oop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_laundry_oop;

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    loyalty_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    position VARCHAR(50) NOT NULL,
    shift VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_code VARCHAR(20) NOT NULL UNIQUE,
    service_name VARCHAR(100) NOT NULL,
    service_type ENUM('kiloan', 'satuan', 'express') NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    extra_info VARCHAR(150) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(25) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    staff_id INT NOT NULL,
    status ENUM('Diterima', 'Dicuci', 'Disetrika', 'Siap Diambil', 'Selesai') DEFAULT 'Diterima',
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    notes TEXT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    service_id INT NOT NULL,
    quantity DECIMAL(8,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    item_notes VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_code VARCHAR(25) NOT NULL UNIQUE,
    order_id INT NOT NULL UNIQUE,
    amount_paid DECIMAL(12,2) NOT NULL,
    payment_method ENUM('Tunai', 'QRIS', 'Transfer Bank') DEFAULT 'Tunai',
    payment_status ENUM('Belum Lunas', 'Lunas') DEFAULT 'Belum Lunas',
    paid_at DATETIME NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Data
INSERT INTO customers (member_code, name, phone, address, loyalty_points) VALUES
('MBR-001', 'Afdal Novana Hendra', '081278901122', 'Jl. Endro Suratmin, Sukarame, Bandar Lampung', 120),
('MBR-002', 'Risa Wulandari', '082188990011', 'Jl. Ryacudu No. 45, Korpri, Bandar Lampung', 85),
('MBR-003', 'Hengki Racheli Pratama', '085266778899', 'Jl. Pulau Sebesi, Sukarame, Bandar Lampung', 50),
('MBR-004', 'Siti Nurhaliza', '081377665544', 'Jl. ZA Pagar Alam, Kedaton, Bandar Lampung', 30);

INSERT INTO staff (staff_code, name, phone, address, position, shift) VALUES
('STF-001', 'Labib Rif''at Besya', '081345678901', 'Jl. ZA Pagar Alam, Bandar Lampung', 'Kasir & Admin', 'Pagi (08:00 - 16:00)'),
('STF-002', 'Hengki Racheli Pratama', '085712345678', 'Jl. Teuku Umar, Bandar Lampung', 'Operator Cuci & Setrika', 'Sore (14:00 - 22:00)');

INSERT INTO services (service_code, service_name, service_type, base_price, extra_info) VALUES
('SRV-K01', 'Cuci Komplit Reguler (Kiloan)', 'kiloan', 7000.00, 'Minimal 2 Kg | Parfum Lavender | Estimasi 48 Jam'),
('SRV-K02', 'Cuci Kering Lipat (Kiloan)', 'kiloan', 5500.00, 'Minimal 2 Kg | Parfum Sakura | Estimasi 48 Jam'),
('SRV-S01', 'Cuci Jas / Kebaya / Bedcover (Satuan)', 'satuan', 25000.00, 'Perawatan Khusus Dry Clean (+Rp 5.000/item) | 72 Jam'),
('SRV-E01', 'Cuci Kilat Express 6 Jam', 'express', 12000.00, 'Prioritas Tinggi | Multiplier 1.25x | Selesai 6 Jam');

INSERT INTO orders (order_code, customer_id, staff_id, status, total_amount, notes) VALUES
('ORD-20261127-001', 1, 1, 'Dicuci', 28000.00, 'Pisahkan pakaian putih agar tidak luntur'),
('ORD-20261127-002', 2, 1, 'Siap Diambil', 60000.00, 'Bedcover ukuran King 2 pcs perawatan khusus'),
('ORD-20261127-003', 3, 2, 'Selesai', 45000.00, 'Seragam kerja butuh cepat 6 jam');

INSERT INTO order_items (order_id, service_id, quantity, subtotal, item_notes) VALUES
(1, 1, 4.00, 28000.00, '4 Kg pakaian harian'),
(2, 3, 2.00, 60000.00, '2 Bedcover besar'),
(3, 4, 3.00, 45000.00, '3 Kg paket express');

INSERT INTO payments (payment_code, order_id, amount_paid, payment_method, payment_status, paid_at) VALUES
('PAY-20261127-001', 1, 28000.00, 'QRIS', 'Lunas', NOW()),
('PAY-20261127-002', 2, 0.00, 'Tunai', 'Belum Lunas', NULL),
('PAY-20261127-003', 3, 45000.00, 'Transfer Bank', 'Lunas', NOW());
