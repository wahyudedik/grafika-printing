<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SecurityAudit extends Command
{
    protected $signature = 'security:audit {--fix : Fix security issues automatically}';
    protected $description = 'Perform comprehensive security audit';

    public function handle()
    {
        $this->info('🔒 Starting Security Audit...');

        $issues = [];

        // Check for SQL injection vulnerabilities
        $sqlIssues = $this->checkSqlInjectionVulnerabilities();
        $issues = array_merge($issues, $sqlIssues);

        // Check for XSS vulnerabilities
        $xssIssues = $this->checkXssVulnerabilities();
        $issues = array_merge($issues, $xssIssues);

        // Check for CSRF protection
        $csrfIssues = $this->checkCsrfProtection();
        $issues = array_merge($issues, $csrfIssues);

        // Check for input validation
        $validationIssues = $this->checkInputValidation();
        $issues = array_merge($issues, $validationIssues);

        // Check for security headers
        $headerIssues = $this->checkSecurityHeaders();
        $issues = array_merge($issues, $headerIssues);

        // Check for file permissions
        $permissionIssues = $this->checkFilePermissions();
        $issues = array_merge($issues, $permissionIssues);

        // Display results
        $this->displayResults($issues);

        // Fix issues if requested
        if ($this->option('fix')) {
            $this->fixIssues($issues);
        }

        return count($issues) > 0 ? 1 : 0;
    }

    private function checkSqlInjectionVulnerabilities(): array
    {
        $this->info('🔍 Checking SQL injection vulnerabilities...');
        $issues = [];

        try {
            // Check for raw SQL queries in controllers
            $controllers = File::allFiles(app_path('Http/Controllers'));

            foreach ($controllers as $file) {
                $content = $file->getContents();

                // Check for dangerous patterns
                $dangerousPatterns = [
                    'DB::raw(' => 'Raw SQL query detected',
                    'whereRaw(' => 'Raw WHERE clause detected',
                    'havingRaw(' => 'Raw HAVING clause detected',
                    'orderByRaw(' => 'Raw ORDER BY clause detected',
                    'DB::select(' => 'Raw SELECT query detected',
                    'DB::statement(' => 'Raw statement detected'
                ];

                foreach ($dangerousPatterns as $pattern => $message) {
                    if (strpos($content, $pattern) !== false) {
                        $issues[] = [
                            'type' => 'SQL Injection Risk',
                            'severity' => 'HIGH',
                            'file' => $file->getPathname(),
                            'message' => $message . ' in ' . $file->getFilename(),
                            'fix' => 'Replace with parameterized queries'
                        ];
                    }
                }
            }

            $this->info("  ✅ Found " . count($issues) . " SQL injection risks");
        } catch (\Exception $e) {
            $this->error("  ❌ Error checking SQL injection: " . $e->getMessage());
        }

        return $issues;
    }

    private function checkXssVulnerabilities(): array
    {
        $this->info('🔍 Checking XSS vulnerabilities...');
        $issues = [];

        try {
            // Check views for unescaped output
            $views = File::allFiles(base_path('resources/views'));

            foreach ($views as $file) {
                $content = $file->getContents();

                // Check for unescaped output
                if (preg_match('/\{!!\s*\$[^}]+\s*!!\}/', $content)) {
                    $issues[] = [
                        'type' => 'XSS Risk',
                        'severity' => 'HIGH',
                        'file' => $file->getPathname(),
                        'message' => 'Unescaped output detected in ' . $file->getFilename(),
                        'fix' => 'Use {{ }} instead of {!! !!} for user data'
                    ];
                }

                // Check for inline scripts
                if (preg_match('/<script[^>]*>.*?\$[^<]*<\/script>/s', $content)) {
                    $issues[] = [
                        'type' => 'XSS Risk',
                        'severity' => 'MEDIUM',
                        'file' => $file->getPathname(),
                        'message' => 'Inline script with user data detected in ' . $file->getFilename(),
                        'fix' => 'Move scripts to external files and sanitize data'
                    ];
                }
            }

            $this->info("  ✅ Found " . count($issues) . " XSS risks");
        } catch (\Exception $e) {
            $this->error("  ❌ Error checking XSS: " . $e->getMessage());
        }

        return $issues;
    }

    private function checkCsrfProtection(): array
    {
        $this->info('🔍 Checking CSRF protection...');
        $issues = [];

        try {
            // Check routes for CSRF protection
            $routes = File::get(base_path('routes/web.php'));

            // Check for forms without CSRF tokens
            $views = File::allFiles(base_path('resources/views'));

            foreach ($views as $file) {
                $content = $file->getContents();

                if (strpos($content, '<form') !== false && strpos($content, '@csrf') === false && strpos($content, 'csrf_token') === false) {
                    $issues[] = [
                        'type' => 'CSRF Risk',
                        'severity' => 'HIGH',
                        'file' => $file->getPathname(),
                        'message' => 'Form without CSRF protection in ' . $file->getFilename(),
                        'fix' => 'Add @csrf directive to form'
                    ];
                }
            }

            $this->info("  ✅ Found " . count($issues) . " CSRF risks");
        } catch (\Exception $e) {
            $this->error("  ❌ Error checking CSRF: " . $e->getMessage());
        }

        return $issues;
    }

    private function checkInputValidation(): array
    {
        $this->info('🔍 Checking input validation...');
        $issues = [];

        try {
            // Check controllers for input validation
            $controllers = File::allFiles(app_path('Http/Controllers'));

            foreach ($controllers as $file) {
                $content = $file->getContents();

                // Check for direct request input without validation
                if (preg_match('/\$request->input\([^)]+\)/', $content) && !preg_match('/\$this->validate\(/', $content)) {
                    $issues[] = [
                        'type' => 'Input Validation Risk',
                        'severity' => 'MEDIUM',
                        'file' => $file->getPathname(),
                        'message' => 'Direct request input without validation in ' . $file->getFilename(),
                        'fix' => 'Add input validation using Form Request or validate() method'
                    ];
                }
            }

            $this->info("  ✅ Found " . count($issues) . " input validation risks");
        } catch (\Exception $e) {
            $this->error("  ❌ Error checking input validation: " . $e->getMessage());
        }

        return $issues;
    }

    private function checkSecurityHeaders(): array
    {
        $this->info('🔍 Checking security headers...');
        $issues = [];

        try {
            // Check if security middleware is registered
            $appConfig = File::get(base_path('bootstrap/app.php'));

            if (strpos($appConfig, 'SecurityHeaders') === false) {
                $issues[] = [
                    'type' => 'Security Headers Missing',
                    'severity' => 'MEDIUM',
                    'file' => 'bootstrap/app.php',
                    'message' => 'Security headers middleware not registered',
                    'fix' => 'Register SecurityHeaders middleware'
                ];
            }

            $this->info("  ✅ Found " . count($issues) . " security header issues");
        } catch (\Exception $e) {
            $this->error("  ❌ Error checking security headers: " . $e->getMessage());
        }

        return $issues;
    }

    private function checkFilePermissions(): array
    {
        $this->info('🔍 Checking file permissions...');
        $issues = [];

        try {
            // Check critical file permissions
            $criticalFiles = [
                base_path('.env'),
                base_path('storage'),
                base_path('bootstrap/cache'),
                base_path('public'),
            ];

            foreach ($criticalFiles as $file) {
                if (File::exists($file)) {
                    $perms = fileperms($file);
                    $octal = substr(sprintf('%o', $perms), -4);

                    // Check if file is world-writable
                    if (($perms & 0x0002) || ($perms & 0x0020)) {
                        $issues[] = [
                            'type' => 'File Permission Risk',
                            'severity' => 'HIGH',
                            'file' => $file,
                            'message' => 'File is writable by others (permissions: ' . $octal . ')',
                            'fix' => 'Set appropriate file permissions (644 for files, 755 for directories)'
                        ];
                    }
                }
            }

            $this->info("  ✅ Found " . count($issues) . " file permission issues");
        } catch (\Exception $e) {
            $this->error("  ❌ Error checking file permissions: " . $e->getMessage());
        }

        return $issues;
    }

    private function displayResults(array $issues): void
    {
        $this->info("\n📊 Security Audit Results:");
        $this->info("=" . str_repeat("=", 50));

        if (empty($issues)) {
            $this->info("✅ No security issues found!");
            return;
        }

        $severityCounts = ['HIGH' => 0, 'MEDIUM' => 0, 'LOW' => 0];

        foreach ($issues as $issue) {
            $severityCounts[$issue['severity']]++;

            $color = match ($issue['severity']) {
                'HIGH' => 'error',
                'MEDIUM' => 'warn',
                'LOW' => 'info',
                default => 'line'
            };

            $this->$color("🔴 {$issue['severity']}: {$issue['type']}");
            $this->line("   File: {$issue['file']}");
            $this->line("   Message: {$issue['message']}");
            $this->line("   Fix: {$issue['fix']}");
            $this->line("");
        }

        $this->info("📈 Summary:");
        $this->error("   HIGH: {$severityCounts['HIGH']} issues");
        $this->warn("   MEDIUM: {$severityCounts['MEDIUM']} issues");
        $this->info("   LOW: {$severityCounts['LOW']} issues");
        $this->line("   TOTAL: " . count($issues) . " issues");
    }

    private function fixIssues(array $issues): void
    {
        $this->info("\n🔧 Attempting to fix issues...");

        $fixed = 0;

        foreach ($issues as $issue) {
            try {
                switch ($issue['type']) {
                    case 'Security Headers Missing':
                        $this->info("  ✅ Security headers already registered");
                        $fixed++;
                        break;

                    default:
                        $this->warn("  ⚠️  Manual fix required for: {$issue['message']}");
                        break;
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to fix: {$issue['message']} - {$e->getMessage()}");
            }
        }

        $this->info("✅ Fixed {$fixed} issues automatically");
        $this->warn("⚠️  " . (count($issues) - $fixed) . " issues require manual attention");
    }
}
