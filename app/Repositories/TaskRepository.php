<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function __construct(protected Task $model) {}

    // Query & manipulasi data task ke database
}