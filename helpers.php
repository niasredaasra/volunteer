<?php
/**
 * Helper functions for cross-environment compatibility
 */

require_once __DIR__ . '/config.php';

/**
 * Get the base URL for the application
 * Works in both local and production environments
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    
    // Clean up the path
    $path = str_replace('\\', '/', $path);
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }
    
    return $protocol . '://' . $host . $path;
}

/**
 * Get environment-specific file paths
 */
function getUploadPath() {
    if (ENVIRONMENT === 'HOSTINGER') {
        return '/home/u231942554/public_html/volunteer_app/uploads/';
    } else {
        return __DIR__ . '/uploads/';
    }
}

/**
 * Generate a relative URL for API calls
 */
function apiUrl($endpoint) {
    return 'api/' . ltrim($endpoint, '/');
}

/**
 * Log messages in environment-appropriate way
 */
function logMessage($message, $level = 'INFO') {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log("[{$level}] " . $message);
    }
    
    // In production, you might want to log to a file
    if (ENVIRONMENT === 'HOSTINGER' && $level === 'ERROR') {
        error_log("[{$level}] " . $message, 3, '/home/u231942554/logs/volunteer_app.log');
    }
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if running in production
 */
function isProduction() {
    return ENVIRONMENT === 'HOSTINGER';
}

/**
 * Get environment-specific cache settings
 */
function getCacheHeaders() {
    if (isProduction()) {
        return [
            'Cache-Control' => 'public, max-age=3600',
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 3600)
        ];
    } else {
        return [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
    }
}
