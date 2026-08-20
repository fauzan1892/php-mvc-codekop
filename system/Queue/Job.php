<?php
declare(strict_types=1);

namespace System\Queue;

use System\Queue\Contracts\ShouldQueue;
use Throwable;

abstract class Job implements ShouldQueue
{
    public string $queue = 'default';
    public int $tries = 3;
    public int|array $backoff = 0;

    public function backoffForAttempt(int $attempt): int
    {
        if (is_array($this->backoff)) {
            $index = max(0, $attempt - 1);
            return max(0, (int) ($this->backoff[$index] ?? end($this->backoff) ?: 0));
        }

        return max(0, $this->backoff);
    }

    public function failed(Throwable $exception): void
    {
    }
}
