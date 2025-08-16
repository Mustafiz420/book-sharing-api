<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $app = require __DIR__ . '/bootstrap/app.php';
    echo "BOOTSTRAP_OK\n";

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "KERNEL_OK\n";
} catch (Throwable $e) {
    echo "ERROR: ", get_class($e), " - ", $e->getMessage(), "\n";
    echo $e->getFile(), ':', $e->getLine(), "\n";
    echo $e->getTraceAsString(), "\n";
}
