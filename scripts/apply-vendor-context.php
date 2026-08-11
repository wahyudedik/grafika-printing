<?php
/**
 * Script to apply HasVendorContext trait and FlashMessage to vendor controllers.
 * Run: php scripts/apply-vendor-context.php
 */

$vendorControllers = [
    // Vendor controllers (use HasVendorContext)
    __DIR__ . '/../app/Http/Controllers/vendor/pos/PosController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/pos/ThermalPrintController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/pos/InvoiceController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/pos/CheckoutController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/pos/PaymentController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/TransaksiController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/LinktreeController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/ProdukController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/PenggunaController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/PelangganController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/SpesifikasiController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/KategoriProdukController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/AlatController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/BahanController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/AuctionBidController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/AbTestController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/TemplateController.php',
    __DIR__ . '/../app/Http/Controllers/vendor/VendorManualTransferController.php',
];

$nonVendorControllers = [
    // Non-vendor controllers (don't use HasVendorContext, just FlashMessage)
    __DIR__ . '/../app/Http/Controllers/VendorWithdrawalController.php',
    __DIR__ . '/../app/Http/Controllers/VendorWalletController.php',
    __DIR__ . '/../app/Http/Controllers/VendorBankAccountController.php',
    __DIR__ . '/../app/Http/Controllers/VendorAuditLogController.php',
    __DIR__ . '/../app/Http/Controllers/VendorRatingController.php',
    __DIR__ . '/../app/Http/Controllers/ShippingCalculatorController.php',
    __DIR__ . '/../app/Http/Controllers/OrderTrackingController.php',
    __DIR__ . '/../app/Http/Controllers/DeliveryConfirmationController.php',
    __DIR__ . '/../app/Http/Controllers/AuctionController.php',
    __DIR__ . '/../app/Http/Controllers/ManualTransferController.php',
    __DIR__ . '/../app/Http/Controllers/PaymentConfirmationController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/AuctionManagementController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/AdminFeeController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/UserLelangController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/WithdrawalManagementController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/WalletManagementController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/ShippingController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/DeliveryController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/MediationController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/CmsController.php',
    __DIR__ . '/../app/Http/Controllers/Admin/ServiceConfigController.php',
];

$traitUse = '    use HasVendorContext;';
$traitImport = 'use App\Http\Concerns\HasVendorContext;';
$flashImport = 'use App\Http\Responses\FlashMessage;';

function processFile(string $filePath, bool $addTrait = false): void
{
    if (!file_exists($filePath)) {
        echo "SKIP (not found): {$filePath}\n";
        return;
    }

    $content = file_get_contents($filePath);
    $original = $content;
    $filename = basename($filePath);

    // Step 1: Add HasVendorContext import (if adding trait)
    if ($addTrait && strpos($content, 'use App\\Http\\Concerns\\HasVendorContext;') === false) {
        // Add after namespace line or after last use statement
        if (preg_match('/^(use [^;]+;)$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
            // Find the last use statement
            preg_match_all('/^use [^;]+;$/m', $content, $allUses, PREG_OFFSET_CAPTURE);
            $lastUse = end($allUses[0]);
            $insertPos = $lastUse[1] + strlen($lastUse[0]);
            $content = substr_replace($content, "\n" . $traitImport, $insertPos, 0);
        } else {
            // Add after opening <?php
            $content = str_replace('<?php', "<?php\n\n" . $traitImport, $content);
        }
    }

    // Step 2: Add FlashMessage import
    if (strpos($content, 'use App\\Http\\Responses\\FlashMessage;') === false) {
        if (preg_match('/^(use [^;]+;)$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
            preg_match_all('/^use [^;]+;$/m', $content, $allUses, PREG_OFFSET_CAPTURE);
            $lastUse = end($allUses[0]);
            $insertPos = $lastUse[1] + strlen($lastUse[0]);
            $content = substr_replace($content, "\n" . $flashImport, $insertPos, 0);
        }
    }

    // Step 3: Add trait use inside class (if adding trait)
    if ($addTrait && strpos($content, 'use HasVendorContext;') === false) {
        // Find class declaration and add trait after opening brace
        if (preg_match('/(class \w+ extends \w+)\s*\{/', $content, $classMatch, PREG_OFFSET_CAPTURE)) {
            $classEndPos = $classMatch[1][1] + strlen($classMatch[0][0]);
            $content = substr_replace($content, "\n" . $traitUse . "\n", $classEndPos, 0);
        }
    }

    // Step 4: Replace vendor context patterns
    // Pattern 1: Auth::user()->vendorUser->first()
    $content = str_replace(
        'Auth::user()->vendorUser->first()',
        '$this->requireVendor()',
        $content
    );

    // Pattern 2: Auth::user()->vendorUser()->first()
    $content = str_replace(
        'Auth::user()->vendorUser()->first()',
        '$this->getVendor()',
        $content
    );

    // Step 5: Replace FlashMessage patterns
    // Pattern: ->with('toast_success', '...')
    $content = preg_replace(
        "/->with\('toast_success',\s*'([^']+)'\)/",
        "->with('toast_success', '$1')",
        $content
    );

    // Step 6: Remove unused Auth import if no more Auth:: references (except Auth::id())
    if (strpos($content, 'Auth::') !== false && preg_match('/Auth::(?!id\(\))/', $content)) {
        // Still uses Auth for other things, keep import
    }

    if ($content !== $original) {
        file_put_contents($filePath, $content);
        echo "UPDATED: {$filename}\n";
    } else {
        echo "NO CHANGE: {$filename}\n";
    }
}

echo "=== Applying HasVendorContext + FlashMessage ===\n\n";

echo "--- Vendor Controllers (with trait) ---\n";
foreach ($vendorControllers as $file) {
    processFile($file, addTrait: true);
}

echo "\n--- Non-Vendor Controllers (FlashMessage only) ---\n";
foreach ($nonVendorControllers as $file) {
    processFile($file, addTrait: false);
}

echo "\n=== Done! ===\n";
