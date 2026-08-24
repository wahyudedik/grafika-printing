<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SecurityService
{
    /**
     * Validate and sanitize input data
     */
    public static function validateInput(array $data, array $rules = []): array
    {
        $sanitized = self::sanitizeArray($data);

        if (!empty($rules)) {
            $validator = Validator::make($sanitized, $rules);

            if ($validator->fails()) {
                Log::warning('Input validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'data' => $data
                ]);

                throw new \InvalidArgumentException('Invalid input data');
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize array data recursively
     */
    public static function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::sanitizeArray($value);
            } elseif (is_string($value)) {
                $data[$key] = self::sanitizeString($value);
            }
        }

        return $data;
    }

    /**
     * Sanitize string input
     */
    public static function sanitizeString(string $input): string
    {
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);

        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);

        // Trim whitespace
        $input = trim($input);

        // Limit length to prevent buffer overflow attacks
        if (strlen($input) > 10000) {
            $input = substr($input, 0, 10000);
        }

        return $input;
    }

    /**
     * Check for SQL injection patterns
     */
    public static function detectSqlInjection(string $input): bool
    {
        $sqlPatterns = [
            '/(\b(ALTER|CREATE|DELETE|DROP|EXEC(UTE)?|INSERT( +INTO)?|MERGE|SELECT|UPDATE|UNION( +ALL)?)\b)/i',
            '/(\b(OR|AND)\s+[\'"]?[\w]+[\'"]?\s*=\s*[\'"]?[\w]+[\'"]?)/i',
            '/(\b(OR|AND)\s+1\s*=\s*1)/i',
            '/(\bUNION\s+SELECT)/i',
            '/(\bSELECT\s+\*)/i',
            '/(\bINSERT\s+INTO)/i',
            '/(\bDELETE\s+FROM)/i',
            '/(\bUPDATE\s+SET)/i',
            '/(\bDROP\s+TABLE)/i'
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                Log::warning('SQL injection attempt detected', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Check for XSS patterns
     */
    public static function detectXss(string $input): bool
    {
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^>]*>/i',
            '/<object\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
            '/<applet\b[^>]*>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i'
        ];

        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                Log::warning('XSS attempt detected', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Check for command injection patterns
     */
    public static function detectCommandInjection(string $input): bool
    {
        $commandPatterns = [
            '/[;&|`$(){}[\]\\]/',
            '/\b(exec|system|shell_exec|passthru|popen|proc_open|eval|assert|create_function)\s*\(/i',
            '/\b(cat|ls|dir|type|more|less|head|tail|grep|find|awk|sed|cut|sort|uniq|wc|ps|kill|killall|chmod|chown|chgrp|mv|cp|rm|mkdir|rmdir|touch|ln|tar|zip|unzip|gzip|gunzip|bzip2|bunzip2|wget|curl|nc|netcat|telnet|ssh|ftp|scp|rsync)\b/i'
        ];

        foreach ($commandPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                Log::warning('Command injection attempt detected', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Check for path traversal patterns
     */
    public static function detectPathTraversal(string $input): bool
    {
        $pathPatterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/i',
            '/%2e%2e%5c/i',
            '/\.\.%2f/i',
            '/\.\.%5c/i'
        ];

        foreach ($pathPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                Log::warning('Path traversal attempt detected', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Comprehensive security check
     */
    public static function checkSecurity(string $input): array
    {
        return [
            'sql_injection' => self::detectSqlInjection($input),
            'xss' => self::detectXss($input),
            'command_injection' => self::detectCommandInjection($input),
            'path_traversal' => self::detectPathTraversal($input)
        ];
    }

    /**
     * Generate secure random string
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password securely
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
        ]);
    }

    /**
     * Verify password securely
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Encrypt sensitive data using Laravel Crypt facade.
     *
     * @deprecated Gunakan EncryptionService untuk enkripsi baru. Method ini dipertahankan
     * untuk backward compatibility dengan data yang sudah ada.
     */
    public static function encrypt(string $data, string $key = null): string
    {
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive data using Laravel Crypt facade.
     *
     * @deprecated Gunakan EncryptionService untuk dekripsi baru. Method ini dipertahankan
     * untuk backward compatibility dengan data yang sudah ada.
     */
    public static function decrypt(string $data, string $key = null): string
    {
        return Crypt::decryptString($data);
    }
}
