#!/usr/bin/env php
<?php
/**
 * FlashMessage Batch Replacement Script v4
 *
 * Replaces ->with('toast_TYPE', 'msg') patterns with FlashMessage:: calls.
 *
 * Strategy:
 *   1. Use preg_match_all to find ALL ->with('toast_...') matches in one pass
 *   2. Process matches in REVERSE offset order (to avoid offset shifting)
 *   3. For each match, scan backward to find the redirect expression
 *   4. Build the correct FlashMessage:: replacement
 *   5. Handle ->withInput() that may follow
 *
 * Patterns handled:
 *   A. redirect()->back()->with('toast_TYPE', 'msg') → FlashMessage::backTYPE('msg')
 *   B. redirect()->route('x')->with('toast_TYPE', 'msg') → FlashMessage::TYPE(redirect()->route('x'), 'msg')
 *   C. view(...)->with('toast_TYPE', 'msg') → SKIPPED
 *   D. Any of the above with ->withInput() appended
 */

$basePath = dirname(__DIR__) . '/app/Http/Controllers';

if (!is_dir($basePath)) {
    fwrite(STDERR, "ERROR: Controllers directory not found: $basePath\n");
    exit(1);
}

// Find all PHP files recursively
$files = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

$totalReplacements = 0;
$filesModified = 0;
$skippedCount = 0;

foreach ($files as $filePath) {
    $content = file_get_contents($filePath);

    // Skip files that don't contain toast_
    if (strpos($content, 'toast_') === false) {
        continue;
    }

    $originalContent = $content;
    $relativePath = str_replace(dirname($basePath) . '/', '', $filePath);
    $replacementsInFile = 0;

    // Step 1: Find ALL matches with offset capture
    // Match: ->with('toast_TYPE', 'message')
    // The message can contain string concatenation (.) and variables
    $pattern = '/->with\(\s*[\'"]toast_(success|error|warning|info)[\'"]\s*,\s*(.+?)\)/s';
    $matches = [];

    if (!preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        continue;
    }

    if (empty($matches)) {
        continue;
    }

    // Step 2: Process matches in REVERSE offset order
    // This avoids offset shifting issues when we modify the string
    $reversedMatches = array_reverse($matches);

    foreach ($reversedMatches as $match) {
        $matchFull   = $match[0][0];  // Full match text: ->with('toast_TYPE', 'msg')
        $matchOffset = $match[0][1];  // Start offset of the match
        $matchLength = strlen($matchFull);
        $toastType   = $match[1][0];  // success, error, warning, info
        $message     = $match[2][0];  // The message string/expression

        // Step 3: Check for ->withInput() AFTER the toast match
        $afterOffset = $matchOffset + $matchLength;
        $afterText = substr($content, $afterOffset, 50);
        $withInputSuffix = '';
        $withInputLength = 0;

        if (preg_match('/^\s*->\s*withInput\(\)/s', $afterText, $wm)) {
            $withInputSuffix = '->withInput()';
            // Calculate the actual length including whitespace
            preg_match('/^\s*->\s*withInput\(\)/s', $afterText, $wm, PREG_OFFSET_CAPTURE);
            $withInputLength = $wm[0][1] + strlen($wm[0][0]);
        }

        // Step 4: Scan BACKWARD from the match to find the redirect expression
        // We look at up to 2000 chars before the match
        $lookbackSize = min(2000, $matchOffset);
        $beforeText = substr($content, $matchOffset - $lookbackSize, $lookbackSize);

        // --- Check for view() context (SKIP) ---
        // view(...) ends with ')' before ->with(
        // We need to check if the immediate predecessor is view(...)
        if (preg_match('/view\s*\([^)]*\)\s*$/s', $beforeText)) {
            $skippedCount++;
            continue;
        }

        // --- Check for redirect()->back() ---
        if (preg_match('/redirect\(\)\s*->\s*back\(\)\s*$/s', $beforeText)) {
            // Find the exact position of redirect()->back()
            $backPos = strrpos($beforeText, 'redirect()->back()');
            if ($backPos === false) {
                // Try with spaces
                preg_match('/redirect\(\)\s*->\s*back\(\)\s*$/s', $beforeText, $bm, PREG_OFFSET_CAPTURE);
                $backPos = isset($bm[0]) ? $bm[0][1] : false;
            }

            if ($backPos !== false) {
                $replaceStart = ($matchOffset - $lookbackSize) + $backPos;
                $replaceEnd = $afterOffset + $withInputLength;
                $replaceLength = $replaceEnd - $replaceStart;

                $fmMethod = 'FlashMessage::back' . ucfirst($toastType);
                $newCode = $fmMethod . '(' . $message . ')';
                if ($withInputSuffix) {
                    $newCode .= ' ' . $withInputSuffix;
                }

                $content = substr_replace($content, $newCode, $replaceStart, $replaceLength);
                $replacementsInFile++;
                continue;
            }
        }

        // --- Check for redirect()->route(...) or redirect()->url(...) ---
        // We need to find the LAST redirect() call before the ->with(
        // The redirect call may span multiple lines and have nested parentheses

        // Strategy: Find the last 'redirect()' and then extract the full expression
        $lastRedirectPos = strrpos($beforeText, 'redirect()');

        if ($lastRedirectPos !== false) {
            // Check what comes after redirect()
            $afterRedirect = substr($beforeText, $lastRedirectPos + strlen('redirect()'));

            // Match ->route(...) or ->url(...)
            if (preg_match('/^\s*->\s*(route|url)\s*\(/s', $afterRedirect, $routeMatch)) {
                // Find the matching closing parenthesis
                $parenStart = $lastRedirectPos + strlen('redirect()') + strlen($routeMatch[0]) - 1; // Position of '('
                $parenDepth = 1;
                $searchFrom = $parenStart + 1;
                $fullBeforeLen = strlen($beforeText);

                while ($parenDepth > 0 && $searchFrom < $fullBeforeLen) {
                    $char = $beforeText[$searchFrom];
                    if ($char === '(') {
                        $parenDepth++;
                    } elseif ($char === ')') {
                        $parenDepth--;
                    } elseif ($char === "'" || $char === '"') {
                        // Skip string contents
                        $quote = $char;
                        $searchFrom++;
                        while ($searchFrom < $fullBeforeLen && $beforeText[$searchFrom] !== $quote) {
                            if ($beforeText[$searchFrom] === '\\') {
                                $searchFrom++; // Skip escaped char
                            }
                            $searchFrom++;
                        }
                    }
                    $searchFrom++;
                }

                if ($parenDepth === 0) {
                    // $searchFrom now points to the char after the closing ')'
                    // The redirect expression is from $lastRedirectPos to $searchFrom
                    $redirectEnd = ($matchOffset - $lookbackSize) + $searchFrom;
                    $redirectExpr = substr($content, $redirectEnd - ($searchFrom - $lastRedirectPos), $searchFrom - $lastRedirectPos);

                    // Trim whitespace
                    $redirectExpr = trim($redirectExpr);

                    $replaceStart = ($matchOffset - $lookbackSize) + $lastRedirectPos;
                    $replaceEnd = $afterOffset + $withInputLength;
                    $replaceLength = $replaceEnd - $replaceStart;

                    $fmMethod = 'FlashMessage::' . $toastType;
                    $newCode = $fmMethod . '(' . $redirectExpr . ', ' . $message . ')';
                    if ($withInputSuffix) {
                        $newCode .= ' ' . $withInputSuffix;
                    }

                    $content = substr_replace($content, $newCode, $replaceStart, $replaceLength);
                    $replacementsInFile++;
                    continue;
                }
            }
        }

        // --- Fallback: Unknown context ---
        fwrite(STDERR, "WARNING: Unknown context in $relativePath at offset $matchOffset\n");
        fwrite(STDERR, "  Match: $matchFull\n");
        $snippet = substr($content, max(0, $matchOffset - 100), 100);
        fwrite(STDERR, "  Before: ..." . $snippet . "\n\n");
        $skippedCount++;
    }

    if ($content !== $originalContent) {
        // Step 5: Add FlashMessage import if not present
        if (strpos($content, 'use App\\Http\\Responses\\FlashMessage;') === false) {
            // Find the namespace line and add import after it
            if (preg_match('/^(namespace [^;]+;)/m', $content, $nsMatch, PREG_OFFSET_CAPTURE)) {
                $insertPos = $nsMatch[0][1] + strlen($nsMatch[0][0]);
                $content = substr_replace($content, "\n\nuse App\\Http\\Responses\\FlashMessage;", $insertPos, 0);
            } else {
                // No namespace found, add after <?php
                $content = str_replace('<?php', "<?php\n\nuse App\\Http\\Responses\\FlashMessage;", $content);
            }
        }

        file_put_contents($filePath, $content);
        $totalReplacements += $replacementsInFile;
        $filesModified++;
        echo "  ✓ $relativePath — $replacementsInFile replacements\n";
    }
}

echo "\n=== Summary ===\n";
echo "Files scanned: " . count($files) . "\n";
echo "Files modified: $filesModified\n";
echo "Total replacements: $totalReplacements\n";
echo "Skipped (view context or unknown): $skippedCount\n";
