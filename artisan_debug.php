<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

try {
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $input = new ArgvInput(['artisan', 'list']);
    $output = new BufferedOutput();

    $kernel->handle($input, $output);

    echo "OK\n";
    echo $output->fetch();
} catch (Throwable $e) {
    $msg = "ERROR: ".get_class($e)." - ".$e->getMessage()."\n".
        $e->getFile().':'.$e->getLine()."\n".
        $e->getTraceAsString()."\n";
    echo $msg;
    file_put_contents(__DIR__."/debug_artisan.txt", $msg);
}
