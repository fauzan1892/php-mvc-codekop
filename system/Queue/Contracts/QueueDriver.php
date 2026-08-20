<?php
declare(strict_types=1);

namespace System\Queue\Contracts;

use System\Queue\JobEnvelope;

interface QueueDriver
{
    public function push(
        string $jobClass,
        string $payload,
        string $queue,
        int $availableAt,
        int $createdAt
    ): string;

    public function reserve(string $queue, int $retryAfter): ?JobEnvelope;

    public function delete(JobEnvelope $job): void;

    public function release(JobEnvelope $job, int $delay): void;

    public function fail(JobEnvelope $job, string $exception): void;
}
