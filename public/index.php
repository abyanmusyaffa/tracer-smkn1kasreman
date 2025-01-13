<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
// if (file_exists($maintenance = __DIR__.'/../../tracer-smkn1kasreman/storage/framework/maintenance.php')) {
//     require $maintenance;
// }

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';
// require __DIR__.'/../../tracer-smkn1kasreman/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
// (require_once __DIR__.'/../../tracer-smkn1kasreman/bootstrap/app.php')
//     ->handleRequest(Request::capture());

//set the public to this directory
// $app->bind('path.public', function() {
//     return __DIR__ ;
//     });