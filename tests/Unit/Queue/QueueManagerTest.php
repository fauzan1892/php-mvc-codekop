<?php
declare(strict_types=1);

namespace Tests\Unit\Queue;

use App\Jobs\ExampleJob;
use PHPUnit\Framework\TestCase;
use System\Queue\QueueManager;

final class QueueManagerTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codekop-manager-' . bin2hex(random_bytes(6));
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->path);
    }

    public function testWorkerDispatchesAndProcessesJobOnce(): void
    {
        $manager = new QueueManager([
            'driver' => 'file',
            'path' => $this->path,
            'default' => 'testing',
            'retry_after' => 10,
            'max_attempts' => 1,
        ]);
        $manager->push(new ExampleJob('phpunit smoke'), 'testing');
        $messages = [];

        $processed = $manager->work(
            'testing',
            true,
            0,
            1,
            static function (string $message) use (&$messages): void {
                $messages[] = $message;
            }
        );

        self::assertSame(1, $processed);
        self::assertCount(1, $messages);
        self::assertStringContainsString('Processed App\\Jobs\\ExampleJob', $messages[0]);
        self::assertSame([], glob($this->path . '/pending/*.json') ?: []);
        self::assertSame([], glob($this->path . '/processing/*.json') ?: []);
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
