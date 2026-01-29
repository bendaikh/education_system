<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Suspicious patterns in URLs that indicate malicious activity
     */
    private array $suspiciousPatterns = [
        'eval(',
        'base64_decode',
        'shell_exec',
        'passthru',
        'system(',
        '../',
        '..\\',
        'etc/passwd',
        'boot.ini',
        'wp-admin',
        'wp-login',
        'wp-content',
        'phpinfo',
        'phpmyadmin',
        'adminer',
        '.php.bak',
        '.php~',
        'config.php.bak',
    ];

    /**
     * Suspicious user agents
     */
    private array $suspiciousAgents = [
        'havij',
        'sqlmap',
        'nikto',
        'nmap',
        'masscan',
        'nessus',
        'openvas',
        'acunetix',
        'netsparker',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = strtolower($request->getRequestUri());
        $userAgent = strtolower($request->userAgent() ?? '');
        $ip = $request->ip();

        // Check for suspicious URL patterns
        foreach ($this->suspiciousPatterns as $pattern) {
            if (str_contains($uri, strtolower($pattern))) {
                $this->logSuspiciousRequest($request, "Suspicious URL pattern: {$pattern}");
                abort(403, 'Forbidden');
            }
        }

        // Check for suspicious user agents
        foreach ($this->suspiciousAgents as $agent) {
            if (str_contains($userAgent, $agent)) {
                $this->logSuspiciousRequest($request, "Suspicious user agent: {$agent}");
                abort(403, 'Forbidden');
            }
        }

        // Block requests trying to access PHP files directly (except index.php)
        if (preg_match('/\.php$/i', $uri) && !preg_match('/^\/index\.php/i', $uri)) {
            // Allow only specific PHP endpoints if needed
            $allowedPhpFiles = [
                '/index.php',
            ];

            $isAllowed = false;
            foreach ($allowedPhpFiles as $allowed) {
                if (str_starts_with($uri, $allowed)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                $this->logSuspiciousRequest($request, "Direct PHP file access attempt");
                abort(403, 'Forbidden');
            }
        }

        // Check for POST requests with suspicious content
        if ($request->isMethod('POST')) {
            $content = $request->getContent();
            foreach (['<?php', '<?=', '<script', 'javascript:', 'vbscript:'] as $dangerous) {
                if (stripos($content, $dangerous) !== false) {
                    // Allow if it's a legitimate code-related endpoint
                    if (!str_contains($uri, '/api/code') && !str_contains($uri, '/admin/settings')) {
                        $this->logSuspiciousRequest($request, "Suspicious POST content: {$dangerous}");
                        abort(403, 'Forbidden');
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Log suspicious request for review
     */
    private function logSuspiciousRequest(Request $request, string $reason): void
    {
        Log::channel('security')->warning('Suspicious request blocked', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'uri' => $request->getRequestUri(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
