<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function indexNotifications()
    {
        $auth = $this->getAuth();
        return $auth->notifications;
//        $user->notify(new TransactionNotification($transactionType, $amount));
    }

    public function indexUnreadNotifications()
    {
        $auth = $this->getAuth();
        return $auth->unreadNotifications;
//        $user->notify(new TransactionNotification($transactionType, $amount));
    }
}
