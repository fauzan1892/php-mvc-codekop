<?php
declare(strict_types=1);

namespace System\Queue;

use System\Queue\Contracts\ShouldQueue;

final class Queue
{
    private static ?QueueManager $manager = null;

    public static function dispatch(ShouldQueue $job, ?string $queue = null, ?int $delay = null): string
    {
        return self::manager()->push($job, $queue, $delay);
    }

    public static function push(ShouldQueue $job, ?string $queue = null, ?int $delay = null): string
    {
        return self::dispatch($job, $queue, $delay);
    }

    public static function work(
        ?string $queue = null,
        bool $once = false,
        int $sleep = 0,
        int $maxJobs = 0,
        ?callable $output = null
    ): int {
        return self::manager()->work($queue, $once, $sleep, $maxJobs, $output);
    }

    public static function manager(): QueueManager
    {
        return self::$manager ??= new QueueManager();
    }
}
