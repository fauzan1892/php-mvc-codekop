<?php
declare(strict_types=1);

namespace System\Queue\Contracts;

interface ShouldQueue
{
    public function handle(): void;
}
