-- Manual Migration: Make bahan_id nullable in transaksi_item_specifications
-- Date: 7 August 2026
-- Reason: bahan_id is NOT NULL but code intentionally passes null for auction specs and custom text specs
--
-- Error fixed: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'bahan_id' cannot be null
--
-- Affected code:
--   - app/Http/Controllers/vendor/TransaksiController.php (lines 195, 397): $specData['bahan_id'] ?? null
--   - app/Services/AuctionToPosService.php (line 254): 'bahan_id' => null (auction specs)

-- Make bahan_id nullable
ALTER TABLE `transaksi_item_specifications` MODIFY COLUMN `bahan_id` BIGINT UNSIGNED NULL;
