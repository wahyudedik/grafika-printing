-- ============================================================
-- Manual Migration: Tambah kolom harga_jual ke tabel produks
-- Tanggal: 7 Agustus 2026
-- ⚠️ PENTING: Jalankan script ini manual di production database!
-- ⚠️ INI BUKAN ARTISAN MIGRATION — jalankan langsung di MySQL
-- ============================================================

-- Cek apakah kolom sudah ada (skip jika sudah)
SET @exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'produks'
    AND COLUMN_NAME = 'harga_jual'
);

-- Tambah kolom harga_jual jika belum ada
SET @sql = IF(
    @exists = 0,
    'ALTER TABLE `produks` ADD COLUMN `harga_jual` decimal(15,2) DEFAULT NULL AFTER `deskripsi`',
    'SELECT "Kolom harga_jual sudah ada, skip." AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verifikasi
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'produks'
ORDER BY ORDINAL_POSITION;
