<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SecurityScan extends Command
{
    protected $signature = 'security:scan {--fix : Automatically delete suspicious files}';
    protected $description = 'Scan for suspicious/malicious PHP files in the application';

    // Directories that should NEVER contain PHP files
    private array $noPhpDirectories = [
        'public/build',
        'public/images',
        'public/uploads',
        'public/assets',
        'storage/app/public',
    ];

    // Directories where PHP files should be small (config, lang, etc.)
    private array $smallPhpDirectories = [
        'config' => 20000,      // Max 20KB for config files
        'lang' => 15000,        // Max 15KB for language files
        'database/factories' => 30000,
        'database/seeders' => 50000,
    ];

    // Suspicious patterns in file content
    private array $suspiciousPatterns = [
        'eval(base64_decode',
        'eval(gzinflate',
        'eval(gzuncompress',
        'shell_exec',
        'system(',
        'passthru(',
        'exec(',
        '$$',
        'assert(',
        'preg_replace.*\/e',
        'create_function',
        'call_user_func',
        '$_FILES',
        'file_put_contents.*<?php',
        'fwrite.*<?php',
        'base64_decode($_',
        'str_rot13',
        'FilesMan',
        'b374k',
        'r57shell',
        'c99shell',
        'WSO',
        'webshell',
        'backdoor',
    ];

    // Known malicious file signatures (first bytes)
    private array $maliciousSignatures = [
        'GIF89a<?php', // PHP hidden in GIF
        '<?php $CONFIG', // Common file manager backdoor
    ];

    public function handle(): int
    {
        $this->info('🔍 Starting security scan...');
        $this->newLine();

        $suspiciousFiles = [];
        $basePath = base_path();

        // Check 1: PHP files in forbidden directories
        $this->info('Checking for PHP files in forbidden directories...');
        foreach ($this->noPhpDirectories as $dir) {
            $fullPath = $basePath . '/' . $dir;
            if (File::isDirectory($fullPath)) {
                $phpFiles = $this->findPhpFiles($fullPath);
                foreach ($phpFiles as $file) {
                    $suspiciousFiles[] = [
                        'path' => $file,
                        'reason' => "PHP file in forbidden directory: {$dir}",
                        'severity' => 'HIGH'
                    ];
                }
            }
        }

        // Check 2: Oversized PHP files in restricted directories
        $this->info('Checking for oversized PHP files...');
        foreach ($this->smallPhpDirectories as $dir => $maxSize) {
            $fullPath = $basePath . '/' . $dir;
            if (File::isDirectory($fullPath)) {
                $phpFiles = $this->findPhpFiles($fullPath);
                foreach ($phpFiles as $file) {
                    $size = File::size($file);
                    if ($size > $maxSize) {
                        $suspiciousFiles[] = [
                            'path' => $file,
                            'reason' => "Oversized file ({$this->formatBytes($size)}) in {$dir} (max: {$this->formatBytes($maxSize)})",
                            'severity' => 'HIGH'
                        ];
                    }
                }
            }
        }

        // Check 3: Recently modified PHP files (last 24 hours) outside vendor
        $this->info('Checking recently modified PHP files...');
        $recentFiles = $this->findRecentPhpFiles($basePath, 1);
        foreach ($recentFiles as $file) {
            // Skip vendor directory
            if (str_contains($file, '/vendor/') || str_contains($file, '\\vendor\\')) {
                continue;
            }
            
            // Check for suspicious content
            $content = File::get($file);
            foreach ($this->suspiciousPatterns as $pattern) {
                if (preg_match('/' . preg_quote($pattern, '/') . '/i', $content)) {
                    $suspiciousFiles[] = [
                        'path' => $file,
                        'reason' => "Contains suspicious pattern: {$pattern}",
                        'severity' => 'CRITICAL'
                    ];
                    break;
                }
            }
        }

        // Check 4: Files with suspicious signatures
        $this->info('Checking for malicious file signatures...');
        $allPhpFiles = $this->findPhpFiles($basePath, ['vendor', 'node_modules']);
        foreach ($allPhpFiles as $file) {
            $firstBytes = File::get($file, false, null, 0, 100);
            foreach ($this->maliciousSignatures as $signature) {
                if (str_contains($firstBytes, $signature)) {
                    $suspiciousFiles[] = [
                        'path' => $file,
                        'reason' => "Malicious file signature detected",
                        'severity' => 'CRITICAL'
                    ];
                    break;
                }
            }
        }

        // Remove duplicates
        $suspiciousFiles = collect($suspiciousFiles)->unique('path')->values()->all();

        // Report findings
        $this->newLine();
        if (empty($suspiciousFiles)) {
            $this->info('✅ No suspicious files found!');
            return Command::SUCCESS;
        }

        $this->error('⚠️  Found ' . count($suspiciousFiles) . ' suspicious file(s):');
        $this->newLine();

        foreach ($suspiciousFiles as $file) {
            $color = $file['severity'] === 'CRITICAL' ? 'red' : 'yellow';
            $this->line("<fg={$color}>[{$file['severity']}]</> {$file['path']}");
            $this->line("         Reason: {$file['reason']}");
            $this->newLine();
        }

        // Log the findings
        Log::warning('Security scan found suspicious files', [
            'count' => count($suspiciousFiles),
            'files' => $suspiciousFiles
        ]);

        // Auto-fix if requested
        if ($this->option('fix')) {
            if ($this->confirm('Do you want to delete these suspicious files?', false)) {
                foreach ($suspiciousFiles as $file) {
                    try {
                        File::delete($file['path']);
                        $this->info("Deleted: {$file['path']}");
                    } catch (\Exception $e) {
                        $this->error("Failed to delete: {$file['path']}");
                    }
                }
            }
        } else {
            $this->info('Run with --fix to delete suspicious files');
        }

        return Command::FAILURE;
    }

    private function findPhpFiles(string $directory, array $exclude = []): array
    {
        $files = [];
        
        if (!File::isDirectory($directory)) {
            return $files;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $path = $file->getPathname();
                
                // Check exclusions
                $excluded = false;
                foreach ($exclude as $excludeDir) {
                    if (str_contains($path, DIRECTORY_SEPARATOR . $excludeDir . DIRECTORY_SEPARATOR)) {
                        $excluded = true;
                        break;
                    }
                }
                
                if (!$excluded) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }

    private function findRecentPhpFiles(string $directory, int $days): array
    {
        $files = [];
        $cutoff = time() - ($days * 24 * 60 * 60);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && 
                strtolower($file->getExtension()) === 'php' && 
                $file->getMTime() > $cutoff) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
