<?php
declare(strict_types=1);

namespace Tests\Unit\Queue;

use PHPUnit\Framework\TestCase;
use System\Queue\FileQueueDriver;
use System\Queue\JobEnvelope;

final class FileQueueDriverTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codekop-queue-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->path);
    }

    public function testJobCanBeReservedAndDeleted(): void
    {
        $driver = new FileQueueDriver($this->path);
        $id = $driver->push('App\\Jobs\\ExampleJob', 'payload', 'default', time(), time());

        $job = $driver->reserve('default', 60);

        self::assertInstanceOf(JobEnvelope::class, $job);
        self::assertSame($id, $job->id);
        self::assertSame(1, $job->attempts);

        $driver->delete($job);

        self::assertSame([], glob($this->path . '/processing/*.json') ?: []);
    }

    public function testReleasedJobIsNotImmediatelyAvailableWhenDelayed(): void
    {
        $driver = new FileQueueDriver($this->path);
        $driver->push('App\\Jobs\\ExampleJob', 'payload', 'default', time(), time());
        $job = $driver->reserve('default', 60);

        self::assertInstanceOf(JobEnvelope::class, $job);
        $driver->release($job, 30);

        self::assertNull($driver->reserve('default', 60));
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) return;
        foreach (glob($path . '/*') ?: [] as $entry) {
            if (is_dir($entry)) {
                $this->removeDirectory($entry);
            } else {
                unlink($entry);
            }
        }
        rmdir($path);
    }
}
