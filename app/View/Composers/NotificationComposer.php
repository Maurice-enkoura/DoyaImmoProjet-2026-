<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        
        $notifications = collect();
        $notificationsCount = 0;

        if ($user) {
            $notifications = $user->notifications()->latest()->take(10)->get();
            $notificationsCount = $user->unreadNotifications()->count();
        }

        $view->with([
            'notifications' => $notifications,
            'notificationsCount' => $notificationsCount,
        ]);
    }
}