<?php

namespace App\Repositories;

use App\Models\DefaultTask;

class DefaultTaskRepository
{
    public function __construct(protected DefaultTask $model) {}

    // Query & manipulasi data default task ke database
}