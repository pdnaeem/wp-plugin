<?php

if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    http_response_code(503);
    echo 'Dependencies are not installed. Run composer install.';
    exit;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
