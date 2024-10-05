<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{user_id}', function ( $user, $user_id ) {
    if ( !$user->active || !$user->allow_messages ) return false;
    if ( $user->role == 1 ) return true;
    return $user->id == $user_id;
});
Broadcast::channel('mail.{user_id}', function ( $user, $user_id ) {
    return $user->id == $user_id;
});
Broadcast::channel('notification', function ( $user ) {
    return $user->role == 1 && $user->allow_reports;
});
