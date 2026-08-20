<?php
declare(strict_types=1);

namespace System\Queue;

use ReflectionClass;
use System\Config;
use System\Queue\Contracts\QueueDriver;
use System\Queue\Contracts\ShouldQueue;
use Throwable;

final class QueueManager
{
    private array $config;
    private QueueDriver $driver;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? (array) Config::get('queue', []);
        $driver = strtolower((string) ($this->config['driver'] ?? 'file'));
        $this->driver = match ($driver) {
            'file' => new FileQueueDriver(
                (string) ($this->config['path'] ?? ROOTPATH . 'storage/queue')
            ),
            'database', 'db' => new DatabaseQueueDriver((array) ($this->config['database'] ?? [])),
            default => throw new QueueException('Unsupported queue driver: ' . $driver),
        };
    }

    public function push(ShouldQueue $job, ?string $queue = null, ?int $delay = null): string
    {
        $class = $job::class;
        if (!str_starts_with($class, 'App\\Jobs\\')) {
            throw new QueueException('Queued jobs must live in the App\\Jobs namespace.');
        }
        $reflection = new ReflectionClass($job);
        if (!$reflection->isInstantiable()) {
            throw new QueueException('Queued job must be instantiable: ' . $class);
        }
        try {
            $payload = base64_encode(serialize($job));
        } catch (Throwable $exception) {
            throw new QueueException('Unable to serialize queued job: ' . $class, 0, $exception);
        }
        $selectedQueue = $queue;
        if ($selectedQueue === null && $job instanceof Job) $selectedQueue = $job->queue;
        $selectedQueue = $selectedQueue ?: (string) ($this->config['default'] ?? 'default');
        if (!preg_match('/^[A-Za-z0-9_-]{1,100}$/', $selectedQueue)) {
            throw new QueueException('Invalid queue name.');
        }

        $availableAt = time() + max(0, $delay ?? (int) ($this->config['delay'] ?? 0));
        return $this->driver->push($class, $payload, $selectedQueue, $availableAt, time());
    }

    public function work(
        ?string $queue = null,
        bool $once = false,
        int $sleep = 0,
        int $maxJobs = 0,
        ?callable $output = null
    ): int {
        $selectedQueue = $queue ?: (string) ($this->config['default'] ?? 'default');
        $sleep = max(0, $sleep ?: (int) ($this->config['sleep'] ?? 3));
        $retryAfter = max(1, (int) ($this->config['retry_after'] ?? 90));
        $processed = 0;

        while ($maxJobs <= 0 || $processed < $maxJobs) {
            $envelope = $this->driver->reserve($selectedQueue, $retryAfter);
            if ($envelope === null) {
                if ($once) break;
                if ($sleep > 0) sleep($sleep);
                continue;
            }
            $processed++;
            try {
                $job = $this->hydrate($envelope);
                $job->handle();
                $this->driver->delete($envelope);
                $this->write($output, 'Processed ' . $envelope->jobClass . ' [' . $envelope->id . ']');
            } catch (Throwable $exception) {
                $job = isset($job) && $job instanceof ShouldQueue ? $job : null;
                $maxAttempts = $job instanceof Job
                    ? max(1, $job->tries)
                    : max(1, (int) ($this->config['max_attempts'] ?? 3));
                if ($envelope->attempts < $maxAttempts) {
                    $delay = $job instanceof Job
                        ? $job->backoffForAttempt($envelope->attempts)
                        : $retryAfter;
                    $this->driver->release($envelope, $delay);
                    $this->write($output, 'Released ' . $envelope->jobClass . ': ' . $exception->getMessage());
                } else {
                    $this->driver->fail($envelope, (string) $exception);
                    if ($job instanceof Job) {
                        try {
                            $job->failed($exception);
                        } catch (Throwable $failedException) {
                            error_log('Queue failed() callback failed: ' . $failedException->getMessage());
                        }
                    }
                    $this->write($output, 'Failed ' . $envelope->jobClass . ': ' . $exception->getMessage());
                }
            }
            unset($job);
            if ($once) break;
        }

        return $processed;
    }

    private function hydrate(JobEnvelope $envelope): ShouldQueue
    {
        if (!str_starts_with($envelope->jobClass, 'App\\Jobs\\')
            || !class_exists($envelope->jobClass)) {
            throw new QueueException('Queued job class is not allowed: ' . $envelope->jobClass);
        }
        $job = unserialize(base64_decode($envelope->payload, true), [
            'allowed_classes' => [$envelope->jobClass],
        ]);
        if (!$job instanceof ShouldQueue) {
            throw new QueueException('Queued payload does not implement ShouldQueue.');
        }
        return $job;
    }

    private function write(?callable $output, string $message): void
    {
        if ($output !== null) {
            $output($message);
            return;
        }
        fwrite(STDOUT, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL);
    }
}
