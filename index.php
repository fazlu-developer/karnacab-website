<?php

/**
 * cPanel fallback when the domain document root is the website/ folder
 * (not website/public). Prefer rewriting via .htaccess; this file handles
 * direct hits to /index.php.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require_once __DIR__.'/bootstrap/show_errors.php';

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

if (! is_file(__DIR__.'/vendor/autoload.php')) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo function_exists('karnacab_error_html')
        ? karnacab_error_html('Missing vendor/autoload.php', 'Upload the website/vendor folder (or run composer install on the server).')
        : 'Missing vendor/autoload.php';
    exit;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
