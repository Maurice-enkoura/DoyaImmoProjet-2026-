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

        $layout = 'layouts.dashboard';
        $view = 'notifications.index';

        if ($user->isAgence()) {
            $layout = 'layouts.dashboard-agence';
        } elseif ($user->isAdmin()) {
            $layout = 'layouts.admin';
        }

        return view($view, compact('notifications', 'layout'));
    }

    public function count()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Marque UNE notification comme lue
     */
    public function read(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        // AJAX → JSON
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // Formulaire → redirection
        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marque TOUTES les notifications comme lues
     */
    public function readAll(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        // AJAX → JSON
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // Formulaire → redirection
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function new(Request $request)
    {
        $user = auth()->user();
        $lastCheck = $request->get('last_check');

        $query = $user->unreadNotifications();

        if ($lastCheck) {
            $query->where('created_at', '>', $lastCheck);
        } else {
            $query->where('created_at', '>', now()->subSeconds(35));
        }

        $notifications = $query->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'title'   => $n->data['title']   ?? 'Notification',
                    'message' => $n->data['message'] ?? '',
                    'type'    => $n->data['type']    ?? 'info',
                    'icon'    => $n->data['icon']    ?? null,
                    'link'    => $n->data['link']    ?? null,
                ];
            }),
            'count' => $user->unreadNotifications()->count(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}