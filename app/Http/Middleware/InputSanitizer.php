<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InputSanitizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Sanitize all input data
        $this->sanitizeInput($request);

        return $next($request);
    }

    /**
     * Sanitize all input data
     */
    private function sanitizeInput(Request $request)
    {
        // Sanitize query parameters
        $query = $request->query->all();
        $request->query->replace($this->sanitizeArray($query));

        // Sanitize POST data
        $post = $request->request->all();
        $request->request->replace($this->sanitizeArray($post));

        // Sanitize cookies
        $cookies = $request->cookies->all();
        $request->cookies->replace($this->sanitizeArray($cookies));

        // Sanitize headers (be careful with this)
        $headers = $request->headers->all();
        foreach ($headers as $key => $values) {
            if (in_array(strtolower($key), ['user-agent', 'referer', 'origin'])) {
                $headers[$key] = $this->sanitizeArray($values);
            }
        }
    }

    /**
     * Recursively sanitize array data
     */
    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->sanitizeString($value);
            }
        }

        return $data;
    }

    /**
     * Sanitize string input
     */
    private function sanitizeString(string $input): string
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

        // Remove potential SQL injection patterns
        $sqlPatterns = [
            '/(\b(ALTER|CREATE|DELETE|DROP|EXEC(UTE)?|INSERT( +INTO)?|MERGE|SELECT|UPDATE|UNION( +ALL)?)\b)/i',
            '/(\b(OR|AND)\s+[\'"]?[\w]+[\'"]?\s*=\s*[\'"]?[\w]+[\'"]?)/i',
            '/(\b(OR|AND)\s+1\s*=\s*1)/i',
            '/(\b(OR|AND)\s+[\'"]?[\w]+[\'"]?\s*LIKE\s*[\'"]?[\w]+[\'"]?)/i',
            '/(\bUNION\s+SELECT)/i',
            '/(\bSELECT\s+\*)/i',
            '/(\bINSERT\s+INTO)/i',
            '/(\bDELETE\s+FROM)/i',
            '/(\bUPDATE\s+SET)/i',
            '/(\bDROP\s+TABLE)/i'
        ];

        foreach ($sqlPatterns as $pattern) {
            $input = preg_replace($pattern, '[BLOCKED]', $input);
        }

        // Remove potential XSS patterns
        $xssPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^>]*>/i',
            '/<object\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
            '/<applet\b[^>]*>/i',
            '/<meta\b[^>]*>/i',
            '/<link\b[^>]*>/i',
            '/<style\b[^>]*>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i',
            '/onreset\s*=/i',
            '/onselect\s*=/i',
            '/onkeydown\s*=/i',
            '/onkeyup\s*=/i',
            '/onkeypress\s*=/i',
            '/onmousedown\s*=/i',
            '/onmouseup\s*=/i',
            '/onmousemove\s*=/i',
            '/onmouseout\s*=/i',
            '/onmousewheel\s*=/i',
            '/ondblclick\s*=/i',
            '/oncontextmenu\s*=/i',
            '/onresize\s*=/i',
            '/onscroll\s*=/i'
        ];

        foreach ($xssPatterns as $pattern) {
            $input = preg_replace($pattern, '[BLOCKED]', $input);
        }

        // Remove potential command injection patterns
        $commandPatterns = [
            '/[;&|`$(){}[\]\\]/',
            '/\b(exec|system|shell_exec|passthru|popen|proc_open|eval|assert|create_function)\s*\(/i',
            '/\b(cat|ls|dir|type|more|less|head|tail|grep|find|awk|sed|cut|sort|uniq|wc|ps|kill|killall|chmod|chown|chgrp|mv|cp|rm|mkdir|rmdir|touch|ln|tar|zip|unzip|gzip|gunzip|bzip2|bunzip2|wget|curl|nc|netcat|telnet|ssh|ftp|scp|rsync)\b/i'
        ];

        foreach ($commandPatterns as $pattern) {
            $input = preg_replace($pattern, '[BLOCKED]', $input);
        }

        // Remove potential path traversal patterns
        $pathPatterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/i',
            '/%2e%2e%5c/i',
            '/\.\.%2f/i',
            '/\.\.%5c/i'
        ];

        foreach ($pathPatterns as $pattern) {
            $input = preg_replace($pattern, '[BLOCKED]', $input);
        }

        return $input;
    }
}
