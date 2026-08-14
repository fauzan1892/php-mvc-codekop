<?php
declare(strict_types=1);

namespace System;

defined('BASEPATH') || exit('No direct script access allowed');

interface Middleware
{
    public function handle(): bool;
}
