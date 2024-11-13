<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function indexNotifications()
    {
        $auth = $this->getAuth();
        return [];
//        $user->notify(new TransactionNotification($transactionType, $amount));
    }

    public function indexUnreadNotifications()
    {
        $auth = $this->getAuth();
        return [];
//        $user->notify(new TransactionNotification($transactionType, $amount));
    }
    public function indexArtistNotifications()
    {
        $auth = $this->getAuth();
        return [];
//        $user->notify(new TransactionNotification($transactionType, $amount));
    }

    public function indexArtistUnreadNotifications()
    {
        $auth = $this->getAuth();
        return [];
//        $user->notify(new TransactionNotification($transactionType, $amount));
    }
}
