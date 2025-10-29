<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ConversationParticipant;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// User-level channel for global notifications/messages
// TEMP: relax rule to diagnose 403 (ensure authenticated user only)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (bool) $user; // authenticated via sanctum
});

// TEMP: relax rule to diagnose 403 (ensure authenticated user only)
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    return (bool) $user; // authenticated via sanctum
});
