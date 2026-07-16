<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return 'Notifications — Coming soon.';
    }

    public function markAsRead(string $id)
    {
        return 'Mark as read — Coming soon.';
    }

    public function markAllAsRead()
    {
        return 'Mark all as read — Coming soon.';
    }
}
