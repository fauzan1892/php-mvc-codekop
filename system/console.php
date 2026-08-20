<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Console commands can only run from CLI.' . PHP_EOL);
}

define('ROOTPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('BASEPATH', ROOTPATH);

require_once ROOTPATH . 'system/Config/Config.php';
require_once ROOTPATH . 'app/Config/Config.php';
require_once ROOTPATH . 'app/Config/Constants.php';
require_once ROOTPATH . 'app/Config/Database.php';

$autoload = ROOTPATH . 'vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

use System\Queue\Queue;

$command = $argv[1] ?? 'list';
if ($command === 'queue:work') {
    $queue = null;
    $once = false;
    $sleep = 0;
    $maxJobs = 0;
    foreach (array_slice($argv, 2) as $argument) {
        if ($argument === '--once') {
            $once = true;
        } elseif (str_starts_with($argument, '--queue=')) {
            $queue = substr($argument, 8) ?: null;
        } elseif (str_starts_with($argument, '--sleep=')) {
            $sleep = max(0, (int) substr($argument, 8));
        } elseif (str_starts_with($argument, '--max-jobs=')) {
            $maxJobs = max(0, (int) substr($argument, 11));
        } else {
            fwrite(STDERR, 'Unknown option: ' . $argument . PHP_EOL);
            exit(2);
        }
    }

    try {
        Queue::work($queue, $once, $sleep, $maxJobs);
        exit(0);
    } catch (Throwable $exception) {
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
        exit(1);
    }
}

fwrite(STDOUT, 'Codekop console' . PHP_EOL);
fwrite(STDOUT, '  queue:work [--queue=name] [--once] [--sleep=3] [--max-jobs=10]' . PHP_EOL);
exit($command === 'list' ? 0 : 1);
