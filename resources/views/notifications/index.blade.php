@extends($layout ?? 'layouts.dashboard')

@section('title', 'Notifications — DoyaImmo')
@section('page_title', 'Notifications')
@section('page_sub', 'Toutes vos notifications')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Notifications</h2>
            <p style="font-size:13.5px; color:var(--muted); margin-top:4px;">
                {{ auth()->user()->unreadNotifications()->count() }} non lues
            </p>
        </div>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-check-double"></i> Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="notifications-list">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    
                    $icon = $data['icon'] ?? 'fa-bell';
                    $type = $data['type'] ?? 'info';
                    
                    $iconColors = [
                        'success' => ['bg' => '#E8F5E9', 'color' => '#1E7A47'],
                        'info' => ['bg' => '#E3F2FD', 'color' => '#0D47A1'],
                        'warning' => ['bg' => '#FFF8E1', 'color' => '#E65100'],
                        'error' => ['bg' => '#FFEBEE', 'color' => '#C62828'],
                        'danger' => ['bg' => '#FFEBEE', 'color' => '#C62828'],
                    ];
                    
                    $colors = $iconColors[$type] ?? ['bg' => '#F7F9FC', 'color' => 'var(--text-soft)'];
                    $timeAgo = $notification->created_at->diffForHumans();
                    
                    // ✅ Déterminer la couleur de bordure pour non lu
                    $unreadClass = $isUnread ? 'unread' : '';
                @endphp
                
                <div class="notif-item {{ $unreadClass }}">
                    <div class="notif-ic" style="background:{{ $colors['bg'] }}; color:{{ $colors['color'] }};">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                    <div class="notif-body">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap;">
                            <div>
                                <b>{{ $data['title'] ?? 'Notification' }}</b>
                                <span>{{ $data['message'] ?? '' }}</span>
                            </div>
                            @if($isUnread)
                                <span class="unread-badge">Nouveau</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;margin-top:6px;flex-wrap:wrap;">
                            <span class="notif-time">{{ $timeAgo }}</span>
                            @if($isUnread)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 10px;">
                                        <i class="fa-regular fa-circle-check"></i> Marquer comme lu
                                    </button>
                                </form>
                            @endif
                            @if(isset($data['link']) && $data['link'])
                                <a href="{{ $data['link'] }}" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 10px;">
                                    <i class="fa-solid fa-arrow-right"></i> Voir
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top:30px;">
            {{ $notifications->links() }}
        </div>
    @else
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-regular fa-bell-slash" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucune notification</p>
            <p style="font-size:13px;max-width:400px;margin:4px auto 0;">Vous n'avez pas encore de notifications. Elles apparaîtront ici lorsque vous recevrez des mises à jour.</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .notifications-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 16px 20px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        transition: all 0.2s;
    }

    .notif-item:hover {
        background: #FAFBFC;
    }

    .notif-item.unread {
        background: #F7F9FC;
        border-left: 4px solid var(--rust);
    }

    .notif-item.unread:hover {
        background: #F0F2F5;
    }

    .notif-ic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .notif-body {
        flex: 1;
        min-width: 0;
    }

    .notif-body b {
        display: block;
        font-size: 14px;
        color: var(--ink);
        margin-bottom: 2px;
    }

    .notif-body span {
        display: block;
        font-size: 13px;
        color: var(--text-soft);
        line-height: 1.5;
    }

    .notif-time {
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
    }

    .unread-badge {
        display: inline-block;
        padding: 2px 10px;
        background: var(--rust);
        color: #fff;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
    }

    .btn-sm {
        padding: 4px 12px;
        font-size: 12px;
    }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }

    .section-head h2 {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
    }

    .pagination li {
        display: inline;
    }

    .pagination a, .pagination span {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: var(--border);
    }

    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .notif-item {
            flex-wrap: wrap;
            gap: 10px;
        }

        .notif-body b {
            font-size: 13px;
        }

        .notif-body span {
            font-size: 12px;
        }

        .notif-ic {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }

        .section-head {
            flex-direction: column;
            align-items: stretch;
        }

        .section-head .btn {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .notif-item {
            padding: 12px 14px;
        }

        .notif-body b {
            font-size: 12px;
        }

        .notif-body span {
            font-size: 11px;
        }

        .notif-ic {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .unread-badge {
            font-size: 9px;
            padding: 1px 8px;
        }

        .notif-time {
            font-size: 10px;
        }

        .btn-sm {
            font-size: 10px;
            padding: 2px 8px;
        }
    }
</style>
@endpush