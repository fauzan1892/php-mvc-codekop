<?php
declare(strict_types=1);

namespace App\Jobs;

use System\Queue\Job;

final class ExampleJob extends Job
{
    public function __construct(public readonly string $message)
    {
    }

    public function handle(): void
    {
        error_log('ExampleJob: ' . $this->message);
    }
}
