<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'role'      => 'string',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'user_id');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function createdDefaultTasks()
    {
        return $this->hasMany(DefaultTask::class, 'created_by');
    }

    // ── Helper Role ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHrStaff(): bool
    {
        return $this->role === 'hr_staff';
    }

    public function isHrAssistant(): bool
    {
        return $this->role === 'hr_assistant';
    }
}