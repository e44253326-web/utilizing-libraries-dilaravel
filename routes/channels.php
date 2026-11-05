<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{userId}', function (User $user, int $userId): bool {
    return $user->id === $userId;
});

Broadcast::channel('admin.transactions', function (User $user): bool {
    return $user->role === 'admin';
});

// TAMBAHAN UNTUK BKPM MINGGU 11 - CHAT REALTIME
Broadcast::channel('chat', function () {
    return true;
});