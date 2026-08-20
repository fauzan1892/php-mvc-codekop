<?php
declare(strict_types=1);

namespace System\Queue;

final class JobEnvelope
{
    public string $id;
    public string $queue;
    public string $jobClass;
    public string $payload;
    public int $attempts;
    public int $availableAt;
    public int $createdAt;

    public function __construct(
        string $id,
        string $queue,
        string $jobClass,
        string $payload,
        int $attempts,
        int $availableAt,
        int $createdAt
    ) {
        $this->id = $id;
        $this->queue = $queue;
        $this->jobClass = $jobClass;
        $this->payload = $payload;
        $this->attempts = $attempts;
        $this->availableAt = $availableAt;
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'queue' => $this->queue,
            'job' => $this->jobClass,
            'payload' => $this->payload,
            'attempts' => $this->attempts,
            'available_at' => $this->availableAt,
            'created_at' => $this->createdAt,
        ];
    }
}
