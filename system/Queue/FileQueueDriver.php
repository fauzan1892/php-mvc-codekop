<?php
declare(strict_types=1);

namespace System\Queue;

use System\Queue\Contracts\QueueDriver;
use Throwable;

final class FileQueueDriver implements QueueDriver
{
    private string $pendingPath;
    private string $processingPath;
    private string $failedPath;

    public function __construct(string $path)
    {
        $this->pendingPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'pending';
        $this->processingPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'processing';
        $this->failedPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'failed';
        foreach ([$this->pendingPath, $this->processingPath, $this->failedPath] as $directory) {
            if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
                throw new QueueException('Unable to create queue directory: ' . $directory);
            }
        }
    }

    public function push(
        string $jobClass,
        string $payload,
        string $queue,
        int $availableAt,
        int $createdAt
    ): string {
        $id = bin2hex(random_bytes(16));
        $this->write($this->pendingPath . DIRECTORY_SEPARATOR . $id . '.json', [
            'id' => $id,
            'queue' => $queue,
            'job' => $jobClass,
            'payload' => $payload,
            'attempts' => 0,
            'available_at' => $availableAt,
            'created_at' => $createdAt,
        ]);

        return $id;
    }

    public function reserve(string $queue, int $retryAfter): ?JobEnvelope
    {
        $this->recoverStale($retryAfter);
        $files = glob($this->pendingPath . DIRECTORY_SEPARATOR . '*.json') ?: [];
        sort($files, SORT_STRING);

        foreach ($files as $source) {
            $data = $this->read($source);
            if ($data === null) {
                $this->quarantine($source);
                continue;
            }
            if ((string) ($data['queue'] ?? '') !== $queue
                || (int) ($data['available_at'] ?? 0) > time()) {
                continue;
            }

            $id = (string) ($data['id'] ?? '');
            if (!preg_match('/^[a-f0-9]{32}$/', $id)) {
                $this->quarantine($source);
                continue;
            }
            $target = $this->processingPath . DIRECTORY_SEPARATOR . $id . '.json';
            if (!rename($source, $target)) {
                continue;
            }

            $data['attempts'] = (int) ($data['attempts'] ?? 0) + 1;
            $data['reserved_at'] = time();
            try {
                $this->write($target, $data);
            } catch (Throwable $exception) {
                @rename($target, $source);
                throw $exception;
            }

            return $this->envelope($data);
        }

        return null;
    }

    public function delete(JobEnvelope $job): void
    {
        $path = $this->processingPath . DIRECTORY_SEPARATOR . $job->id . '.json';
        if (is_file($path) && !unlink($path)) {
            throw new QueueException('Unable to remove processed queue job: ' . $job->id);
        }
    }

    public function release(JobEnvelope $job, int $delay): void
    {
        $source = $this->processingPath . DIRECTORY_SEPARATOR . $job->id . '.json';
        $target = $this->pendingPath . DIRECTORY_SEPARATOR . $job->id . '.json';
        $data = $job->toArray();
        $data['available_at'] = time() + max(0, $delay);
        unset($data['reserved_at']);
        $this->write($source, $data);
        if (!rename($source, $target)) {
            throw new QueueException('Unable to release queue job: ' . $job->id);
        }
    }

    public function fail(JobEnvelope $job, string $exception): void
    {
        $source = $this->processingPath . DIRECTORY_SEPARATOR . $job->id . '.json';
        $target = $this->failedPath . DIRECTORY_SEPARATOR . $job->id . '.json';
        $data = $job->toArray();
        $data['failed_at'] = time();
        $data['exception'] = substr($exception, 0, 20000);
        $this->write($source, $data);
        if (!rename($source, $target)) {
            throw new QueueException('Unable to move failed queue job: ' . $job->id);
        }
    }

    private function recoverStale(int $retryAfter): void
    {
        $threshold = time() - max(1, $retryAfter);
        $files = glob($this->processingPath . DIRECTORY_SEPARATOR . '*.json') ?: [];
        foreach ($files as $source) {
            $data = $this->read($source);
            if ($data === null || (int) ($data['reserved_at'] ?? 0) > $threshold) {
                continue;
            }
            unset($data['reserved_at']);
            $data['available_at'] = time();
            $id = (string) ($data['id'] ?? '');
            if (preg_match('/^[a-f0-9]{32}$/', $id)) {
                $this->write($source, $data);
                @rename($source, $this->pendingPath . DIRECTORY_SEPARATOR . $id . '.json');
            }
        }
    }

    private function envelope(array $data): JobEnvelope
    {
        return new JobEnvelope(
            (string) $data['id'],
            (string) $data['queue'],
            (string) $data['job'],
            (string) $data['payload'],
            (int) $data['attempts'],
            (int) $data['available_at'],
            (int) $data['created_at']
        );
    }

    private function read(string $path): ?array
    {
        $contents = @file_get_contents($path);
        if (!is_string($contents)) return null;
        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            return is_array($data) ? $data : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function write(string $path, array $data): void
    {
        try {
            $contents = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $exception) {
            throw new QueueException('Unable to encode queue payload.', 0, $exception);
        }
        $temporary = $path . '.tmp.' . bin2hex(random_bytes(8));
        if (file_put_contents($temporary, $contents, LOCK_EX) === false
            || !rename($temporary, $path)) {
            @unlink($temporary);
            throw new QueueException('Unable to write queue payload: ' . $path);
        }
        chmod($path, 0640);
    }

    private function quarantine(string $source): void
    {
        $target = $this->failedPath . DIRECTORY_SEPARATOR . basename($source) . '.invalid';
        @rename($source, $target);
    }
}
