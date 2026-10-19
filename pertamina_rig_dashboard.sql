-- =====================================================================
-- Pertamina Admin Panel — Rig Monitoring WK Zona 4
-- Database schema + seed data (MySQL / MariaDB — XAMPP / phpMyAdmin)
-- Generated: 2026-09-29
-- Charset: utf8mb4 | Engine: InnoDB
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `pertamina_rig_dashboard`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pertamina_rig_dashboard`;

-- ---------------------------------------------------------------------
-- lokasi — kode lapangan operasional (menu Lokasi)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `jadwal`;
DROP TABLE IF EXISTS `alat`;
DROP TABLE IF EXISTS `lokasi`;
CREATE TABLE `lokasi` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kode` VARCHAR(20) NOT NULL,
  `nama_tempat` VARCHAR(150) NOT NULL,
  `provinsi` VARCHAR(100) NOT NULL,
  `kota` VARCHAR(100) NULL,
  `kecamatan` VARCHAR(100) NULL,
  `keterangan` TEXT NULL,
  `tanggal_dipetakan` DATE NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_lokasi_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lokasi` (`kode`,`nama_tempat`,`provinsi`,`kota`,`kecamatan`,`keterangan`,`tanggal_dipetakan`) VALUES
('MJ','Lapangan MJ','Sumatera Selatan',NULL,NULL,'Cluster sumur MJ-A9, MJ-A21, MJ-A22, MJ-OS2.','2026-07-30'),
('RSS','Lapangan Rosella Shallow South','Sumatera Selatan',NULL,NULL,'Sumur eksplorasi shallow.','2026-07-30'),
('TAP','Lapangan TAP','Sumatera Selatan',NULL,NULL,'Cluster sumur TAP-NW.','2026-07-30'),
('BKB','Lapangan BKB','Sumatera Selatan',NULL,NULL,'Cluster sumur BKB-AR.','2026-07-30'),
('BEL','Lapangan BEL','Sumatera Selatan',NULL,NULL,'Cluster sumur BEL-K.','2026-07-30'),
('KRG','Lapangan KRG','Sumatera Selatan',NULL,NULL,'Cluster sumur KRG-PN/PK/PB/HZ.','2026-07-30'),
('TMB','Lapangan TMB','Sumatera Selatan',NULL,NULL,'Cluster sumur pengganti TMB-PG.','2026-07-30'),
('TLJT','Lapangan TLJT','Sumatera Selatan',NULL,NULL,'Sumur TLJT-04.','2026-07-30'),
('TTB','Lapangan TTB','Sumatera Selatan',NULL,NULL,'Sumur TTB-A1.','2026-07-30'),
('ABB','Lapangan ABB','Sumatera Selatan',NULL,NULL,'Cluster sumur ABB-A.','2026-07-30'),
('LVT','Lapangan LVT','Sumatera Selatan',NULL,NULL,'Sumur LVT-J.','2026-07-30'),
('LBA','Lapangan LBA','Sumatera Selatan',NULL,NULL,'Sumur LBA-X13.','2026-07-30'),
('BRG','Lapangan BRG','Sumatera Selatan',NULL,NULL,'Sumur BRG-U9.','2026-07-30'),
('BKT','Lapangan BKT','Sumatera Selatan',NULL,NULL,'Cluster sumur BKT-AW.','2026-07-30');

-- ---------------------------------------------------------------------
-- alat — rig & alat pendukung (menu Alat)
-- ---------------------------------------------------------------------
CREATE TABLE `alat` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `kategori` ENUM('rig','alat_pendukung') NOT NULL,
  `merek` VARCHAR(100) NOT NULL,
  `model` VARCHAR(100) NULL,
  `tahun_perolehan` YEAR NULL,
  `no_seri` VARCHAR(50) NOT NULL,
  `status` ENUM('aktif','standby','bermasalah') NOT NULL DEFAULT 'aktif',
  `keterangan` TEXT NULL,
  `foto` VARCHAR(255) NULL COMMENT 'path/URL foto alat — belum diisi (masih placeholder ikon di UI)',
  `lokasi_id` INT UNSIGNED NULL COMMENT 'lapangan tempat rig ini saat ini bertugas (NULL utk alat pendukung / rig standby)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_alat_no_seri` (`no_seri`),
  KEY `idx_alat_kategori` (`kategori`),
  KEY `idx_alat_status` (`status`),
  CONSTRAINT `fk_alat_lokasi` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `alat` (`nama`,`kategori`,`merek`,`model`,`tahun_perolehan`,`no_seri`,`status`,`keterangan`,`lokasi_id`) VALUES
('PDSI #36.1/SKYTOP650-M','rig','PDSI','SKYTOP650-M',2016,'RIG-3610-2016','aktif','Rig utama pengeboran Zona 4, saat ini pekerjaan Well Intervention.',1),
('PDSI #16.2/NT45-M','rig','PDSI','NT45-M',2014,'RIG-1620-2014','aktif','Menangani sumur MJ-A22 dan MJ-A9.',1),
('PDSI #17.2/NT45-M','rig','PDSI','NT45-M',2014,'RIG-1720-2014','aktif','Sumur eksplorasi Rosella Shallow South.',2),
('AIRLANGGA #55/550 HP','rig','Airlangga Drilling','550 HP',2011,'RIG-5500-2011','bermasalah','Kontrak rig berakhir, menunggu proses re-tender.',NULL),
('TMMJ #23/550 HP','rig','TMMJ','550 HP',2012,'RIG-2300-2012','standby','Proses pengadaan, rencana inplace Zona 1 Juni 2026.',1),
('PDSI #18.2/LTO-650','rig','PDSI','LTO-650',2015,'RIG-1820-2015','aktif','Konstruksi lokasi BKB-AR.016 & BKB-AR.012.',4),
('PDSI #20.2/EMSCOD2-M','rig','PDSI','EMSCOD2-M',2013,'RIG-2020-2013','aktif','BEL-K8, BEL-K9, KRG-PN2/PN4.',5),
('PDSI #25.2/LTO750-M','rig','PDSI','LTO750-M',2017,'RIG-2520-2017','aktif','Konstruksi lokasi BKB-AR.008, menunggu penyelesaian tanam tumbuh.',4),
('ACS #21/750 HP','rig','ACS','750 HP',2018,'RIG-2100-2018','aktif','KRG-PK2, amandemen PJWK.',6),
('AMS #01/750 HP ex WBR #5','rig','AMS','750 HP',2010,'RIG-0100-2010','aktif','Eks WBR #5, menangani KRG-PB8 dan BEL-K10.',6),
('UNSPEC RIG #4/750 HP','rig','Belum ditentukan','750 HP',NULL,'RIG-0004-TBD','standby','Proses pengadaan re-tender, est. inplace Agustus 2026.',NULL),
('PDSI #01.2/N80B-M','rig','PDSI','N80B-M',2012,'RIG-0120-2012','aktif','Konstruksi sumur pengganti TMB-PG3/5/7, TLJT-04.',7),
('PDSI #05.2/OW760-M','rig','PDSI','OW760-M',2016,'RIG-0520-2016','aktif','TTB-A1, ABB-A5, LVT-J.',9),
('PDSI #30.2/D1000-E','rig','PDSI','D1000-E',2019,'RIG-3020-2019','aktif','LBA-X13, KRG-PB5/HZ1.',12),
('PDSI #32.2/N80UE','rig','PDSI','N80UE',2013,'RIG-3220-2013','aktif','ABB-A6 amandemen kontrak, BRG-U9.',10),
('TMMJ #05/1000 HP','rig','TMMJ','1000 HP',2011,'RIG-0500-2011','aktif','BKT-AW2, BKT-AW3, survei sosialisasi berjalan.',14),
('Genset Cummins 500kVA','alat_pendukung','Cummins','C500D5',2019,'GEN-0192-2019','aktif','Sumber daya cadangan lokasi konstruksi.',NULL),
('Mud Pump Triplex','alat_pendukung','National Oilwell Varco','NOV 12-P-160',2018,'MPT-0455-2018','aktif','Digunakan bergantian antar rig sesuai jadwal pemboran.',NULL),
('Crane Crawler 80T','alat_pendukung','Kobelco','CK850G',2020,'CRN-0080-2020','aktif','Mobilisasi alat berat ke lokasi konstruksi baru.',NULL),
('Compressor Udara','alat_pendukung','Atlas Copco','XATS 900',2021,'CMP-0900-2021','standby','Belum ditugaskan, standby di gudang Zona 4.',NULL),
('BOP Stack','alat_pendukung','Cameron','U II 13-5/8"',2017,'BOP-1358-2017','aktif','Blowout preventer, wajib terpasang saat operasi pemboran.',NULL),
('Top Drive System','alat_pendukung','National Oilwell Varco','TDS-11SA',2016,'TDS-1101-2016','bermasalah','Perlu maintenance sebelum digunakan kembali.',NULL),
('Mobile Workshop Unit','alat_pendukung','Mercedes-Benz','Actros 3340',2015,'MWU-3340-2015','aktif','Unit bengkel keliling untuk perbaikan lapangan.',NULL),
('Water Truck','alat_pendukung','Hino','FM 260 JD',2019,'WTR-0260-2019','aktif','Suplai air untuk kebutuhan konstruksi & operasional lokasi.',NULL);

-- ---------------------------------------------------------------------
-- users — akun admin & pekerja (menu Manajemen User / Profil)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `user_sessions`;
DROP TABLE IF EXISTS `user_permissions`;
DROP TABLE IF EXISTS `connected_apps`;
DROP TABLE IF EXISTS `audit_log`;
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'gunakan password_hash() PHP / bcrypt — placeholder di seed ini',
  `jabatan` ENUM('administrator','pekerja') NOT NULL DEFAULT 'pekerja',
  `status` ENUM('online','offline') NOT NULL DEFAULT 'offline' COMMENT 'idealnya diturunkan dari user_sessions aktif, kolom ini cache/snapshot',
  `foto_profil` VARCHAR(255) NULL COMMENT 'path/URL foto — belum diisi, UI masih pakai avatar inisial',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_jabatan` (`jabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`nama`,`username`,`email`,`password_hash`,`jabatan`,`status`) VALUES
('Budi Santoso','budi.santoso','budi.santoso@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','administrator','online'),
('Siti Rahayu','siti.rahayu','siti.rahayu@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','administrator','offline'),
('Hendra Gunawan','hendra.gunawan','hendra.gunawan@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','administrator','online'),
('Agus Wijaya','agus.wijaya','agus.wijaya@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','online'),
('Dewi Lestari','dewi.lestari','dewi.lestari@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','online'),
('Fajar Ramadhan','fajar.ramadhan','fajar.ramadhan@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','offline'),
('Rina Kartika','rina.kartika','rina.kartika@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','online'),
('Putri Ayu','putri.ayu','putri.ayu@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','offline'),
('Yusuf Hidayat','yusuf.hidayat','yusuf.hidayat@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','online'),
('Maya Anggraini','maya.anggraini','maya.anggraini@gmail.com','$2y$10$PLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDERPLACEHOLDE','pekerja','offline');

-- ---------------------------------------------------------------------
-- user_permissions — hak akses per modul (toggle Alat/Lokasi/Jadwal)
-- ---------------------------------------------------------------------
CREATE TABLE `user_permissions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `modul` ENUM('alat','lokasi','jadwal') NOT NULL,
  `diizinkan` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_user_modul` (`user_id`,`modul`),
  CONSTRAINT `fk_userperm_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_permissions` (`user_id`,`modul`,`diizinkan`) VALUES
(1,'alat',1),
(1,'lokasi',1),
(1,'jadwal',1),
(2,'alat',1),
(2,'lokasi',1),
(2,'jadwal',1),
(3,'alat',1),
(3,'lokasi',1),
(3,'jadwal',1),
(4,'alat',1),
(4,'lokasi',0),
(4,'jadwal',1),
(5,'alat',0),
(5,'lokasi',1),
(5,'jadwal',1),
(6,'alat',1),
(6,'lokasi',0),
(6,'jadwal',0),
(7,'alat',0),
(7,'lokasi',0),
(7,'jadwal',1),
(8,'alat',0),
(8,'lokasi',1),
(8,'jadwal',0),
(9,'alat',1),
(9,'lokasi',0),
(9,'jadwal',1),
(10,'alat',0),
(10,'lokasi',0),
(10,'jadwal',0);

-- ---------------------------------------------------------------------
-- user_sessions — sesi aktif (panel Sesi Aktif di menu Profil)
-- ---------------------------------------------------------------------
CREATE TABLE `user_sessions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `device_info` VARCHAR(150) NOT NULL COMMENT 'cth. Chrome 153.0.0.0 · Windows',
  `ip_address` VARCHAR(45) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_active_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NOT NULL,
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_sessions` (`user_id`,`device_info`,`ip_address`,`created_at`,`last_active_at`,`expires_at`) VALUES
(1,'Chrome 153.0.0.0 · Windows','114.10.42.249','2026-09-27 16:33:00','2026-09-29 09:12:00','2026-10-04 16:33:00');

-- ---------------------------------------------------------------------
-- connected_apps — Perangkat & Aplikasi Terhubung (panel Profil, saat ini kosong)
-- ---------------------------------------------------------------------
CREATE TABLE `connected_apps` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `nama_aplikasi` VARCHAR(100) NOT NULL,
  `scope` VARCHAR(150) NULL,
  `connected_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `terakhir_digunakan` TIMESTAMP NULL,
  CONSTRAINT `fk_connapp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Belum ada data — sesuai kondisi UI saat ini ("Belum ada perangkat/aplikasi terhubung")

-- ---------------------------------------------------------------------
-- dokumentasi_panel / dokumentasi_item — legend (menu Dokumentasi)
-- CRUD di level panel (bisa tambah panel baru, mis. "Legend Strategi")
-- dan di level item (label + gaya tampilan + warna)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `dokumentasi_item`;
DROP TABLE IF EXISTS `dokumentasi_panel`;
CREATE TABLE `dokumentasi_panel` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT NULL,
  `urutan` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dokumentasi_item` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `panel_id` INT UNSIGNED NOT NULL,
  `label` VARCHAR(150) NOT NULL,
  `gaya` ENUM('solid','dashed','border') NOT NULL DEFAULT 'solid',
  `warna` CHAR(7) NOT NULL COMMENT 'hex color, cth. #3ec776',
  `urutan` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_dokitem_panel` FOREIGN KEY (`panel_id`) REFERENCES `dokumentasi_panel`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dokumentasi_panel` (`judul`,`deskripsi`,`urutan`) VALUES
('Kesiapan Operasional','Warna blok pada Gantt Jadwal & Dashboard menandakan fase kerja sumur saat ini.',0),
('Kesiapan Administrasi','Gaya border pada blok Gantt menandakan estimasi window persetujuan FID per kuartal.',1);

INSERT INTO `dokumentasi_item` (`panel_id`,`label`,`gaya`,`warna`,`urutan`) VALUES
(1,'Sudah Tajak','solid','#3ec776',0),
(1,'MIRU / RFD (Ready For Drilling)','solid','#f0a93b',1),
(1,'Konstruksi','solid','#f4d03f',2),
(1,'Lahan Bebas','solid','#8fd9a8',3),
(1,'Proses Pembayaran / Pengajuan KUS-APT','solid','#4b9bf0',4),
(1,'Finalisasi KJPP','solid','#c084f5',5),
(1,'Pengajuan ARS / Survei Sosialisasi','solid','#3ecfc9',6),
(1,'Proses Pengadaan Rig','solid','#a5a3ab',7),
(1,'Rig Acceptance','solid','#9fc6ee',8),
(1,'Lain-lain','solid','#e6d9bd',9),
(1,'Sumur Eksplorasi / Kompleks','solid','#221417',10),
(1,'Kontrak Habis','solid','#5a3438',11),
(2,'Est. Approval FID Jan – Mar','dashed','#f0a93b',0),
(2,'Est. Approval FID Apr – Jun','dashed','#c084f5',1),
(2,'Est. Approval FID Jul – Sep','border','#4b9bf0',2),
(2,'Est. Approval FID Oct – Dec','border','#ea2027',3);

-- ---------------------------------------------------------------------
-- jadwal — item pekerjaan sumur per rig (menu Jadwal / Gantt)
-- ---------------------------------------------------------------------
CREATE TABLE `jadwal` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `alat_id` INT UNSIGNED NOT NULL COMMENT 'rig yang mengerjakan',
  `lokasi_id` INT UNSIGNED NULL COMMENT 'lapangan tempat sumur berada (NULL jika belum ada sumur spesifik, mis. proses pengadaan rig)',
  `kategori_id` INT UNSIGNED NULL COMMENT 'FK ke dokumentasi_item (panel Kesiapan Operasional) — menentukan warna blok',
  `label` VARCHAR(150) NOT NULL COMMENT 'nama sumur / pekerjaan',
  `status` ENUM('selesai','progres','kendala') NOT NULL DEFAULT 'progres',
  `periode_mulai` DATE NOT NULL,
  `durasi_bulan` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `catatan` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_jadwal_status` (`status`),
  CONSTRAINT `fk_jadwal_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_lokasi` FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `dokumentasi_item`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `jadwal` (`alat_id`,`lokasi_id`,`kategori_id`,`label`,`status`,`periode_mulai`,`durasi_bulan`,`catatan`) VALUES
(1,1,10,'Pekerjaan WI Zona 4','progres','2026-06-01',6,'Menyelesaikan pekerjaan WI sebelum lanjut ke sumur eksplorasi PHE Jambi Merang.'),
(1,1,11,'MJ-A21 (CF 2026)','progres','2027-03-01',2,'Est. tajak M3 Desember 2026, on progress persiapan tender rig.'),
(2,1,1,'MJ-A22 29.07-IP','selesai','2026-07-01',1,'IP 29 Juli 2026 — completed sesuai plan.'),
(2,1,3,'MJ-A9 (CF 2026)','progres','2027-02-01',2,'CF 2025→2026, konstruksi lokasi berjalan.'),
(3,2,11,'ROSELLA SHALLOW SOUTH (CO)','progres','2026-06-01',3,'Change Order, sumur eksplorasi.'),
(3,3,6,'TAP-NW1 (CF 2025,2026)','progres','2027-02-01',2,'Finalisasi KJPP tanam tumbuh.'),
(4,NULL,12,'Kontrak Habis','kendala','2026-06-01',2,'Kontrak rig berakhir, perlu proses re-tender sebelum lanjut sumur berikutnya.'),
(5,NULL,8,'Proses Pengadaan Rig — Inplace Jun 2026','kendala','2026-06-01',5,'On progress persiapan tender, rencana beroperasi di Zona 1 akhir tahun; MJ-OS2 dikaji rig lain sementara.'),
(5,1,10,'MJ-OS2 (CF 2025)','progres','2026-12-01',1,'Sudah dihindari lokasi sengketa, est. penyampaian nilai JKPP M4 Agustus 2026.'),
(6,4,3,'BKB-AR.016 (CF 2025)','progres','2026-07-01',2,'Est. selesai konstruksi M4 September 2026.'),
(6,4,5,'BKB-AR.012 (CF 2025,2026)','kendala','2026-10-01',2,'Masih negosiasi nilai tanam tumbuh lahan sawah, perlu konstruksi jembatan.'),
(7,5,1,'BEL-K9 19.06-IP','selesai','2026-06-01',1,'Est. release 5 Agustus 2026.'),
(7,5,1,'BEL-K8','selesai','2026-07-01',1,'Sudah tajak.'),
(7,6,3,'KRG-PN2 / PN4','progres','2026-08-01',2,'Konstruksi lokasi berjalan.'),
(8,4,3,'Konstruksi (Penggantian Tanam Tumbuh)','kendala','2026-06-01',2,'Menunggu penyelesaian isu tanam tumbuh sebelum lanjut konstruksi.'),
(8,4,1,'BKB-AR.008','selesai','2026-09-01',1,'Sudah tajak.'),
(9,6,1,'KRG-PK2 20.05-IP','selesai','2026-06-01',2,'IP tercapai sesuai jadwal.'),
(9,6,7,'Amandemen PJWK','progres','2026-09-01',1,'Proses administrasi amandemen kontrak.'),
(10,6,3,'KRG-PB8','progres','2026-07-01',1,'Konstruksi lokasi berjalan.'),
(10,5,10,'BEL-K10','progres','2026-10-01',2,'Pembayaran tanam tumbuh lokasi existing masih menunggu memo lanjut konstruksi tahap 1.'),
(11,NULL,8,'Proses Pengadaan Rig (Re-Tender) — Est. Agustus 2026','kendala','2026-06-01',5,'Rig acceptance menyusul setelah proses tender ulang selesai.'),
(12,7,3,'Konstruksi Lokasi (Sumur Pengganti)','progres','2026-06-01',3,'Konstruksi lokasi sumur pengganti RK 2026.'),
(12,8,1,'TLJT-04','selesai','2026-09-01',1,'Sudah tajak.'),
(13,9,10,'TTB-A1 (CF 2025)','progres','2026-07-01',1,'Est. release 12/13 Agustus 2026.'),
(13,10,1,'ABB-A5','selesai','2026-08-01',1,'Sudah tajak.'),
(13,11,6,'LVT-J (CF 2025)','progres','2026-12-01',1,'Finalisasi KJPP.'),
(14,12,10,'LBA-X13 (CF 2023,2025)','progres','2026-08-01',1,'On progress pengajuan reposisi SS.'),
(14,6,10,'KRG-PB5/HZ1 (CF 2026)','progres','2026-11-01',1,'M2 Juli 2026 sudah permohonan pengajuan PLK ke SKK Migas.'),
(15,10,10,'ABB-A6 — Amandemen Kontrak','progres','2026-08-01',1,'Amandemen kontrak dan evaluasi kesiapan rig.'),
(15,13,5,'BRG-U9','progres','2026-10-01',1,'Est. lahan bebas M1 Agustus 2026.'),
(16,14,7,'BKT-AW2','progres','2026-11-01',1,'Menggunakan slot UKL-UPL BKT-C (sebelumnya BKT-E).'),
(16,14,7,'BKT-AW3','progres','2026-12-01',1,'On progress survei sosialisasi, est. lahan bebas M2 September 2026, tetap gunakan slot UKL-UPL BKT-F.');

-- Sanity check: total 32 item -> selesai=7, progres=20, kendala=5

-- ---------------------------------------------------------------------
-- audit_log — riwayat aktivitas (menu Audit Log)
-- kolom *_snapshot disimpan terpisah dari users supaya riwayat tetap
-- utuh walau akun user aslinya sudah diedit/dihapus
-- ---------------------------------------------------------------------
CREATE TABLE `audit_log` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL COMMENT 'NULL jika akun sudah dihapus, data tetap ada di kolom snapshot',
  `username_snapshot` VARCHAR(50) NOT NULL,
  `email_snapshot` VARCHAR(150) NOT NULL,
  `jabatan_snapshot` ENUM('administrator','pekerja') NOT NULL,
  `aksi` ENUM('login','logout','tambah','edit','hapus') NOT NULL,
  `modul` ENUM('alat','lokasi','jadwal','user','sistem') NOT NULL,
  `keterangan` TEXT NOT NULL,
  `waktu` DATETIME NOT NULL,
  KEY `idx_audit_waktu` (`waktu`),
  KEY `idx_audit_aksi` (`aksi`),
  KEY `idx_audit_modul` (`modul`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `audit_log` (`user_id`,`username_snapshot`,`email_snapshot`,`jabatan_snapshot`,`aksi`,`modul`,`keterangan`,`waktu`) VALUES
(1,'budi.santoso','budi.santoso@gmail.com','administrator','login','sistem','Login ke sistem','2026-09-29 09:12:00'),
(1,'budi.santoso','budi.santoso@gmail.com','administrator','edit','jadwal','Mengedit status item jadwal BKB-AR.012 menjadi Kendala','2026-09-29 09:20:00'),
(5,'dewi.lestari','dewi.lestari@gmail.com','pekerja','login','sistem','Login ke sistem','2026-09-29 09:05:00'),
(5,'dewi.lestari','dewi.lestari@gmail.com','pekerja','tambah','lokasi','Menambahkan kode lapangan baru: KRG-PJ1','2026-09-29 09:31:00'),
(4,'agus.wijaya','agus.wijaya@gmail.com','pekerja','tambah','alat','Menambahkan alat baru: Compressor Udara Atlas Copco XATS 900','2026-09-28 16:44:00'),
(4,'agus.wijaya','agus.wijaya@gmail.com','pekerja','edit','alat','Mengedit status BOP Stack Cameron menjadi Aktif','2026-09-28 16:50:00'),
(7,'rina.kartika','rina.kartika@gmail.com','pekerja','edit','jadwal','Mengubah durasi pekerjaan KRG-PK8 menjadi 2 bulan','2026-09-28 14:22:00'),
(7,'rina.kartika','rina.kartika@gmail.com','pekerja','login','sistem','Login ke sistem','2026-09-28 14:02:00'),
(2,'siti.rahayu','siti.rahayu@gmail.com','administrator','tambah','user','Menambahkan akun user baru: Maya Anggraini (@maya.anggraini)','2026-09-27 20:15:00'),
(2,'siti.rahayu','siti.rahayu@gmail.com','administrator','edit','user','Mengatur hak akses Fajar Ramadhan: Kelola Alat diaktifkan','2026-09-27 20:22:00'),
(2,'siti.rahayu','siti.rahayu@gmail.com','administrator','logout','sistem','Logout dari sistem','2026-09-27 20:40:00'),
(9,'yusuf.hidayat','yusuf.hidayat@gmail.com','pekerja','edit','jadwal','Mengubah status TAP-NW1 menjadi Selesai','2026-09-27 11:08:00'),
(9,'yusuf.hidayat','yusuf.hidayat@gmail.com','pekerja','login','sistem','Login ke sistem','2026-09-27 10:55:00'),
(8,'putri.ayu','putri.ayu@gmail.com','pekerja','edit','lokasi','Melengkapi alamat kota/kecamatan lapangan BEL','2026-09-26 15:37:00'),
(8,'putri.ayu','putri.ayu@gmail.com','pekerja','login','sistem','Login ke sistem','2026-09-26 15:20:00'),
(3,'hendra.gunawan','hendra.gunawan@gmail.com','administrator','hapus','alat','Menghapus data alat: Water Truck Hino FM 260 JD (rusak permanen, sudah dipensiunkan)','2026-09-26 09:48:00'),
(3,'hendra.gunawan','hendra.gunawan@gmail.com','administrator','login','sistem','Login ke sistem','2026-09-26 09:30:00'),
(6,'fajar.ramadhan','fajar.ramadhan@gmail.com','pekerja','edit','alat','Mengedit keterangan Genset Cummins 500kVA','2026-09-25 13:12:00'),
(6,'fajar.ramadhan','fajar.ramadhan@gmail.com','pekerja','login','sistem','Login ke sistem','2026-09-25 12:58:00'),
(1,'budi.santoso','budi.santoso@gmail.com','administrator','hapus','jadwal','Menghapus item jadwal duplikat: KRG-PN3 (CF 2025)','2026-09-24 17:05:00'),
(1,'budi.santoso','budi.santoso@gmail.com','administrator','login','sistem','Login ke sistem','2026-09-24 08:55:00'),
(5,'dewi.lestari','dewi.lestari@gmail.com','pekerja','logout','sistem','Logout dari sistem','2026-09-23 19:41:00'),
(5,'dewi.lestari','dewi.lestari@gmail.com','pekerja','tambah','jadwal','Menambahkan item jadwal baru: KRG-PK5 (CF 2027)','2026-09-23 16:18:00'),
(2,'siti.rahayu','siti.rahayu@gmail.com','administrator','edit','user','Mengubah jabatan Yusuf Hidayat dari Pekerja tetap sebagai Pekerja (verifikasi ulang)','2026-09-22 10:27:00'),
(10,'maya.anggraini','maya.anggraini@gmail.com','pekerja','login','sistem','Login pertama kali ke sistem','2026-09-21 08:40:00');

-- ---------------------------------------------------------------------
-- target_rk — target & realisasi tajak/completed WK Zona 4 per tahun
-- (angka riil dari file Excel RK Bor 2026/2027 30 Juli 2026, dipakai
-- di card "Target RK Bor" & progress bar realisasi di Dashboard)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `target_rk`;
CREATE TABLE `target_rk` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tahun` YEAR NOT NULL,
  `tajak_target` INT UNSIGNED NULL,
  `completed_target` INT UNSIGNED NULL,
  `operasional_target` INT UNSIGNED NULL,
  `tajak_realisasi` INT UNSIGNED NULL,
  `tajak_rkap` INT UNSIGNED NULL,
  `tajak_wpb` INT UNSIGNED NULL,
  `completed_realisasi` INT UNSIGNED NULL,
  `completed_rkap` INT UNSIGNED NULL,
  `completed_wpb` INT UNSIGNED NULL,
  UNIQUE KEY `uq_target_tahun` (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `target_rk`
(`tahun`,`tajak_target`,`completed_target`,`operasional_target`,`tajak_realisasi`,`tajak_rkap`,`tajak_wpb`,`completed_realisasi`,`completed_rkap`,`completed_wpb`) VALUES
(2025, NULL, NULL, NULL, 43, 69, 100, 39, 76, 100),
(2026, 86, 83, 94, NULL, NULL, NULL, NULL, NULL, NULL),
(2027, 44, 55, 55, NULL, NULL, NULL, NULL, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;

