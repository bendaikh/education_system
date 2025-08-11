<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will require this file so that any prerendered template can be shown
| instead of starting the framework, which could cause an exception.
|
*/

// Smart path detection for both local and production
$maintenancePath = file_exists(__DIR__.'/storage/framework/maintenance.php') 
    ? __DIR__.'/storage/framework/maintenance.php'  // Production (root level)
    : __DIR__.'/../storage/framework/maintenance.php'; // Local (public folder)

if (file_exists($maintenancePath)) {
    require $maintenancePath;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

// Smart path detection for autoloader
$autoloadPath = file_exists(__DIR__.'/vendor/autoload.php')
    ? __DIR__.'/vendor/autoload.php'  // Production (root level)
    : __DIR__.'/../vendor/autoload.php'; // Local (public folder)

require $autoloadPath;

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

// Smart path detection for bootstrap
$bootstrapPath = file_exists(__DIR__.'/bootstrap/app.php')
    ? __DIR__.'/bootstrap/app.php'  // Production (root level)
    : __DIR__.'/../bootstrap/app.php'; // Local (public folder)

$app = require_once $bootstrapPath;

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
