<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(20);
        
        // ✅ Déterminer le layout en fonction du rôle
        $layout = 'layouts.dashboard';
        $view = 'notifications.index';
        
        if ($user->isAgence()) {
            $layout = 'layouts.dashboard-agence';
            $view = 'notifications.index';
        } elseif ($user->isAdmin()) {
            $layout = 'layouts.admin';
            $view = 'notifications.index';
        }
        
        return view($view, compact('notifications', 'layout'));
    }

    public function count()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    public function read($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    public function new(Request $request)
    {
        $notifications = auth()->user()->notifications()
            ->where('created_at', '>', $request->get('last_check', now()->subMinutes(5)))
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'title' => $n->data['title'] ?? 'Notification',
                    'message' => $n->data['message'] ?? '',
                    'type' => $n->data['type'] ?? 'info',
                    'icon' => $n->data['icon'] ?? null,
                    'link' => $n->data['link'] ?? null,
                ];
            }),
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }
}