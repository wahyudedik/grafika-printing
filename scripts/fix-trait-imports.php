<?php
/**
 * Fix: Add HasVendorContext trait import + usage to all vendor controllers
 * that use $this->requireVendor() or $this->getVendor()
 */

$vendorControllers = [
    // POS controllers
    'app/Http/Controllers/vendor/pos/PosController.php',
    'app/Http/Controllers/vendor/pos/ThermalPrintController.php',
    'app/Http/Controllers/vendor/pos/InvoiceController.php',
    'app/Http/Controllers/vendor/pos/CheckoutController.php',
    'app/Http/Controllers/vendor/pos/PaymentController.php',

    // Main vendor controllers
    'app/Http/Controllers/vendor/TransaksiController.php',
    'app/Http/Controllers/vendor/LinktreeController.php',
    'app/Http/Controllers/vendor/ProdukController.php',
    'app/Http/Controllers/vendor/PenggunaController.php',
    'app/Http/Controllers/vendor/PelangganController.php',
    'app/Http/Controllers/vendor/SpesifikasiController.php',
    'app/Http/Controllers/vendor/KategoriProdukController.php',
    'app/Http/Controllers/vendor/AlatController.php',
    'app/Http/Controllers/vendor/BahanController.php',
    'app/Http/Controllers/vendor/AuctionBidController.php',
    'app/Http/Controllers/vendor/AbTestController.php',
    'app/Http/Controllers/vendor/TemplateController.php',
    'app/Http/Controllers/vendor/VendorManualTransferController.php',
];

$traitImport = 'use App\Http\Concerns\HasVendorContext;';
$traitUsage = '    use HasVendorContext;';

$updated = 0;
$skipped = 0;

foreach ($vendorControllers as $file) {
    $fullPath = $file;
    if (!file_exists($fullPath)) {
        echo "NOT FOUND: $file\n";
        continue;
    }

    $content = file_get_contents($fullPath);

    // Check if already has the trait
    if (strpos($content, 'HasVendorContext') !== false) {
        echo "ALREADY HAS TRAIT: " . basename($file) . "\n";
        $skipped++;
        continue;
    }

    // Check if class uses $this->requireVendor() or $this->getVendor()
    if (strpos($content, '$this->requireVendor()') === false &&
        strpos($content, '$this->getVendor()') === false &&
        strpos($content, '$this->getVendorId()') === false) {
        echo "NO TRAIT METHODS: " . basename($file) . "\n";
        $skipped++;
        continue;
    }

    // Add import after last 'use' statement before class declaration
    // Find the pattern: use ...;\n...\nclass
    $lines = explode("\n", $content);
    $lastUseLine = -1;
    $classLine = -1;

    for ($i = 0; $i < count($lines); $i++) {
        $trimmed = trim($lines[$i]);
        if (strpos($trimmed, 'use ') === 0 && substr($trimmed, -1) === ';') {
            $lastUseLine = $i;
        }
        if (preg_match('/^class\s+\w+/', $trimmed)) {
            $classLine = $i;
            break;
        }
    }

    if ($lastUseLine === -1 || $classLine === -1) {
        echo "COULD NOT FIND USE/CLASS: " . basename($file) . "\n";
        $skipped++;
        continue;
    }

    // Insert trait import after last use statement
    array_splice($lines, $lastUseLine + 1, 0, [$traitImport]);

    // Rebuild content and find class line again (shifted by 1)
    $content = implode("\n", $lines);

    // Now find the class opening brace and add trait usage
    // Pattern: class ClassName extends Controller\n{\n
    $content = preg_replace(
        '/(class\s+\w+\s+extends\s+\w+\s*\n\{)/',
        "$1\n$traitUsage",
        $content,
        1
    );

    file_put_contents($fullPath, $content);
    echo "FIXED: " . basename($file) . "\n";
    $updated++;
}

echo "\n=== Done! Updated: $updated, Skipped: $skipped ===\n";
