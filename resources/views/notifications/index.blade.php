@extends($layout ?? 'layouts.dashboard-agence')

@section('title', 'Notifications — DoyaImmo')
@section('page_title', 'Notifications')
@section('page_sub', 'Toutes vos notifications')

@section('content')
<div class="view active notif-page">

    @php
        $totalNotif  = $notifications->total() ?? $notifications->count();
        $unreadCount = auth()->user()->unreadNotifications()->count();
        $hasUnread   = $unreadCount > 0;
        $readCount   = max(0, $totalNotif - $unreadCount);
    @endphp

    {{-- ═══ STATS INLINE (mobile) ═══ --}}
    @if($totalNotif > 0)
        <div class="notif-stats">
            <div class="notif-stat">
                <span class="notif-stat__value">{{ $totalNotif }}</span>
                <span class="notif-stat__label">Total</span>
            </div>
            <div class="notif-stat {{ $hasUnread ? 'is-highlight' : '' }}">
                <span class="notif-stat__value">{{ $unreadCount }}</span>
                <span class="notif-stat__label">Non lues</span>
            </div>
            <div class="notif-stat">
                <span class="notif-stat__value">{{ $readCount }}</span>
                <span class="notif-stat__label">Lues</span>
            </div>
        </div>
    @endif

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <header class="notif-header">
        <div class="notif-header__left">
            <h1 class="notif-header__title">Notifications</h1>
            <div class="notif-header__meta">
                @if($hasUnread)
                    <span class="notif-counter notif-counter--unread">
                        <i class="fa-solid fa-circle"></i>
                        {{ $unreadCount }} non lue{{ $unreadCount > 1 ? 's' : '' }}
                    </span>
                @else
                    <span class="notif-counter notif-counter--all-read">
                        <i class="fa-solid fa-check-double"></i>
                        Tout est à jour
                    </span>
                @endif

                <span class="notif-header__total">
                    {{ $totalNotif }} notification{{ $totalNotif > 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        @if($hasUnread)
            <form action="{{ route('notifications.read-all') }}" method="POST" class="notif-header__action">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Tout marquer comme lu</span>
                </button>
            </form>
        @endif
    </header>

    {{-- ═══ BOUTON "TOUT MARQUER" MOBILE ═══ --}}
    @if($hasUnread)
        <form action="{{ route('notifications.read-all') }}" method="POST" class="notif-mark-all">
            @csrf
            <button type="submit" class="notif-mark-all__btn">
                <i class="fa-solid fa-check-double"></i>
                Tout marquer comme lu
                <span class="notif-mark-all__count">{{ $unreadCount }}</span>
            </button>
        </form>
    @endif

    {{-- ═══ LISTE ═══ --}}
    @if($notifications->count() > 0)
        <div class="notif-list">
            @foreach($notifications as $notification)
                @php
                    $data     = $notification->data;
                    $isUnread = is_null($notification->read_at);

                    $icon = $data['icon'] ?? 'fa-bell';
                    $type = $data['type'] ?? 'info';

                    // Normalisation des types
                    if ($type === 'error') $type = 'danger';
                    if (!in_array($type, ['success', 'info', 'warning', 'danger'])) $type = 'info';

                    $title   = $data['title']   ?? 'Notification';
                    $message = $data['message'] ?? '';

                    // ✅ Le lien est null si absent, vide ou '#'
                    $rawLink = $data['link'] ?? null;
                    $link = ($rawLink && $rawLink !== '#' && $rawLink !== '') ? $rawLink : null;

                    $timeAgo = $notification->created_at->diffForHumans();
                @endphp

                <article class="notif-card notif-card--{{ $type }} {{ $isUnread ? 'is-unread' : 'is-read' }}">

                    {{-- Icône --}}
                    <div class="notif-card__icon">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>

                    {{-- Contenu --}}
                    <div class="notif-card__body">

                        <header class="notif-card__head">
                            <h3 class="notif-card__title">{{ $title }}</h3>

                            @if($isUnread)
                                <span class="notif-card__badge">
                                    <i class="fa-solid fa-circle"></i>
                                    Nouveau
                                </span>
                            @endif
                        </header>

                        @if($message)
                            <p class="notif-card__message">{{ $message }}</p>
                        @endif

                        <footer class="notif-card__foot">
                            <span class="notif-card__time">
                                <i class="fa-regular fa-clock"></i>
                                {{ $timeAgo }}
                            </span>

                            <div class="notif-card__actions">
                                {{-- Marquer comme lu (seulement si non lue) --}}
                                @if($isUnread)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost">
                                            <i class="fa-regular fa-circle-check"></i>
                                            <span>Marquer comme lu</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- ✅ Voir (seulement si lien valide) --}}
                                @if($link)
                                    <a href="{{ $link }}" class="btn btn-rust">
                                        <i class="fa-solid fa-arrow-right"></i>
                                        <span>Voir</span>
                                    </a>
                                @endif

                                {{-- ✅ Message informatif si rien à faire --}}
                                @if(!$isUnread && !$link)
                                    <span class="notif-card__done">
                                        <i class="fa-solid fa-check-circle"></i>
                                        <span>Notification lue</span>
                                    </span>
                                @endif
                            </div>
                        </footer>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- ═══ PAGINATION JS ═══ --}}
        @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator && $notifications->hasPages())
            <div class="pagination-wrapper" id="paginationWrapper"
                 data-current-page="{{ $notifications->currentPage() }}"
                 data-last-page="{{ $notifications->lastPage() }}"
                 data-base-url="{{ $notifications->url(1) }}"
                 data-total="{{ $notifications->total() }}"
                 data-query="{{ http_build_query(request()->except('page')) }}">
            </div>
        @endif

    @else

        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-bell-slash"></i>
            </div>
            <h3>Aucune notification</h3>
            <p>
                Vous n'avez pas encore de notifications. Elles apparaîtront ici
                lorsque vous recevrez des mises à jour.
            </p>
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('paginationWrapper');
    if (!wrapper) return;

    const currentPage = parseInt(wrapper.dataset.currentPage, 10);
    const lastPage    = parseInt(wrapper.dataset.lastPage, 10);
    const baseUrl     = wrapper.dataset.baseUrl;
    const queryString = wrapper.dataset.query;
    const total       = parseInt(wrapper.dataset.total, 10) || 0;

    if (lastPage <= 1) return;

    const buildUrl = (page) => {
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('page', page);
        if (queryString) {
            const params = new URLSearchParams(queryString);
            params.forEach((value, key) => {
                if (key !== 'page') url.searchParams.set(key, value);
            });
        }
        return url.toString();
    };

    const getPages = () => {
        const pages = [];
        const maxVisible = 5;
        if (lastPage <= maxVisible + 2) {
            for (let i = 1; i <= lastPage; i++) pages.push(i);
        } else {
            pages.push(1);
            if (currentPage > 3) pages.push('...');
            const start = Math.max(2, currentPage - 1);
            const end = Math.min(lastPage - 1, currentPage + 1);
            for (let i = start; i <= end; i++) pages.push(i);
            if (currentPage < lastPage - 2) pages.push('...');
            pages.push(lastPage);
        }
        return pages;
    };

    const pages = getPages();
    let html = '<nav class="pagination-nav" role="navigation" aria-label="Pagination">';
    html += '<ul class="pagination-list">';

    if (currentPage > 1) {
        html += `<li class="pagination-item">
                    <a href="${buildUrl(currentPage - 1)}" rel="prev" class="pagination-link">
                        <i class="fa-solid fa-chevron-left"></i>
                        <span class="pagination-label">Précédent</span>
                    </a>
                </li>`;
    } else {
        html += `<li class="pagination-item is-disabled">
                    <span class="pagination-link">
                        <i class="fa-solid fa-chevron-left"></i>
                        <span class="pagination-label">Précédent</span>
                    </span>
                </li>`;
    }

    pages.forEach(p => {
        if (p === '...') {
            html += `<li class="pagination-item is-disabled">
                        <span class="pagination-link pagination-link--dots">…</span>
                     </li>`;
        } else if (p === currentPage) {
            html += `<li class="pagination-item is-active" aria-current="page">
                        <span class="pagination-link">${p}</span>
                     </li>`;
        } else {
            html += `<li class="pagination-item">
                        <a href="${buildUrl(p)}" class="pagination-link">${p}</a>
                     </li>`;
        }
    });

    if (currentPage < lastPage) {
        html += `<li class="pagination-item">
                    <a href="${buildUrl(currentPage + 1)}" rel="next" class="pagination-link">
                        <span class="pagination-label">Suivant</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                 </li>`;
    } else {
        html += `<li class="pagination-item is-disabled">
                    <span class="pagination-link">
                        <span class="pagination-label">Suivant</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                 </li>`;
    }

    html += '</ul>';

    const firstItem = (currentPage - 1) * 15 + 1;
    const lastItem  = Math.min(currentPage * 15, total);
    if (total) {
        html += `<div class="pagination-info">
                    <span class="pagination-info__text">
                        <strong>${firstItem}</strong> – <strong>${lastItem}</strong> sur <strong>${total}</strong> notifications
                    </span>
                 </div>`;
    }

    html += '</nav>';
    wrapper.innerHTML = html;
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   NOTIFICATIONS — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.notif-page {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;

    width: 100%;
    max-width: 100%;
}

.notif-stats { display: none; }
.notif-mark-all { display: none; }

/* ═══ HEADER DESKTOP ═══ */
.notif-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 22px;
    flex-wrap: wrap;
    width: 100%;
}
.notif-header__left { min-width: 0; }
.notif-header__title {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
    line-height: 1.2;
}
.notif-header__meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 13px;
}
.notif-counter {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.notif-counter i { font-size: 7px; }
.notif-counter--unread {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border: 1px solid #FFE0B2;
}
.notif-counter--all-read {
    background: var(--c-success-bg);
    color: var(--c-success);
    border: 1px solid #C8E6C9;
}
.notif-counter--all-read i { font-size: 11px; }
.notif-header__total { color: var(--muted); font-weight: 500; }
.notif-header__action { flex-shrink: 0; }

/* ═══ LISTE ═══ */
.notif-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

/* ═══ CARTE ═══ */
.notif-card {
    position: relative;
    display: flex;
    gap: 14px;
    padding: 14px 18px 14px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    overflow: hidden;
}
.notif-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
    border-color: #d8d8d8;
}

.notif-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--muted);
}
.notif-card--success::before { background: var(--c-success); }
.notif-card--info::before    { background: var(--c-info); }
.notif-card--warning::before { background: var(--c-warning); }
.notif-card--danger::before  { background: var(--c-danger); }

.notif-card.is-unread {
    background: #FFFCF9;
    border-color: rgba(181, 80, 42, .2);
}
.notif-card.is-unread:hover { background: #FFF9F4; }

.notif-card__icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    align-self: flex-start;
    margin-top: 2px;
}
.notif-card--success .notif-card__icon { background: var(--c-success-bg); color: var(--c-success); }
.notif-card--info    .notif-card__icon { background: var(--c-info-bg);    color: var(--c-info); }
.notif-card--warning .notif-card__icon { background: var(--c-warning-bg); color: var(--c-warning); }
.notif-card--danger  .notif-card__icon { background: var(--c-danger-bg);  color: var(--c-danger); }

.notif-card__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.notif-card__head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.notif-card__title {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.3;
    min-width: 0;
}

.notif-card__badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    border-radius: 999px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(181, 80, 42, .25);
}
.notif-card__badge i { font-size: 5px; }

.notif-card__message {
    font-size: 13px;
    line-height: 1.55;
    color: var(--text-soft);
    margin: 0;
    word-break: break-word;
}

.notif-card__foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 4px;
    padding-top: 10px;
    border-top: 1px dashed var(--border);
}

.notif-card__time {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    color: var(--muted);
    font-weight: 500;
}
.notif-card__time i { font-size: 10px; opacity: .8; }

.notif-card__actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    flex-shrink: 0;
    align-items: center;
}

/* ✅ Badge "Notification lue" quand pas d'action */
.notif-card__done {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
    color: var(--muted);
    background: var(--surface);
    border: 1px solid var(--border);
}
.notif-card__done i {
    color: var(--c-success);
    font-size: 13px;
}

.inline-form { display: inline; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(181, 80, 42, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: var(--surface);
    border-color: var(--rust);
    color: var(--rust);
}
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 70px 24px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
    width: 100%;
}
.empty-state__icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: var(--c-info-bg);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 0 auto;
    max-width: 420px;
    line-height: 1.55;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 28px;
    display: flex;
    justify-content: center;
    width: 100%;
}
.pagination-nav {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    width: 100%;
    max-width: 720px;
}
.pagination-list {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    padding: 6px;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
}
.pagination-item { display: inline-flex; }
.pagination-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 38px;
    height: 38px;
    padding: 0 12px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    text-decoration: none;
    background: transparent;
    border: none;
    font-family: inherit;
    transition: all .15s ease;
    white-space: nowrap;
}
.pagination-link:hover { background: var(--surface); color: var(--rust); }
.pagination-item.is-active .pagination-link {
    background: var(--rust);
    color: #fff;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    font-weight: 700;
}
.pagination-item.is-disabled .pagination-link {
    opacity: .4;
    cursor: not-allowed;
    color: var(--muted);
}
.pagination-item.is-disabled .pagination-link:hover { background: transparent; color: var(--muted); }
.pagination-link--dots { pointer-events: none; color: var(--muted); min-width: 32px; }
.pagination-link i { font-size: 11px; }
.pagination-info { text-align: center; }
.pagination-info__text {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.pagination-info__text strong {
    color: var(--text);
    font-weight: 700;
    font-family: var(--display);
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .notif-page { background: transparent; padding-top: 4px; }

    .notif-header { display: none; }

    /* ─── STATS INLINE ─── */
    .notif-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .notif-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        position: relative;
        padding: 0 4px;
    }
    .notif-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .notif-stat__value {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 800;
        color: var(--ios-label);
        letter-spacing: -.5px;
        line-height: 1;
    }
    .notif-stat.is-highlight .notif-stat__value { color: var(--rust); }
    .notif-stat__label {
        font-size: 11px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
    }

    /* ─── BOUTON TOUT MARQUER iOS ─── */
    .notif-mark-all {
        display: block;
        margin-bottom: 14px;
    }
    .notif-mark-all__btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 14px 18px;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        color: var(--ios-blue);
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease, background .15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .notif-mark-all__btn:active {
        transform: scale(.98);
        background: #EFEFF4;
    }
    .notif-mark-all__btn i {
        font-size: 15px;
        color: var(--c-success);
    }
    .notif-mark-all__count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 8px;
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        border-radius: 999px;
        font-family: var(--display);
        font-size: 12px;
        font-weight: 800;
        box-shadow: 0 2px 6px rgba(181, 80, 42, .3);
    }

    /* ─── CARTES iOS ─── */
    .notif-list { gap: 10px; }

    .notif-card {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease;
        overflow: hidden;
        position: relative;
        margin-bottom: 0;
    }
    .notif-card:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); border: none; }
    .notif-card:active { transform: scale(.985); }

    .notif-card::before {
        top: 0; left: 0; right: 0; bottom: auto;
        width: auto;
        height: 3px;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }

    .notif-card.is-unread {
        background: linear-gradient(135deg, #FFF9F4 0%, #FFFCF9 100%);
        border: none;
    }

    .notif-card__icon {
        position: absolute;
        top: 16px;
        left: 16px;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        font-size: 18px;
    }

    .notif-card__body {
        padding-left: 60px;
        gap: 6px;
    }

    .notif-card__head {
        gap: 8px;
        margin-bottom: 2px;
    }
    .notif-card__title {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
        line-height: 1.3;
    }
    .notif-card__badge {
        font-size: 10px;
        padding: 3px 9px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .notif-card__badge i { font-size: 6px; }

    .notif-card__message {
        font-size: 13.5px;
        color: var(--ios-label-2);
        line-height: 1.5;
    }

    .notif-card__foot {
        margin-top: 8px;
        padding-top: 10px;
        border-top: 0.5px solid var(--ios-separator);
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        padding-left: 0;
    }

    .notif-card__time {
        font-size: 12.5px;
        color: var(--ios-label-3);
        padding-left: 0;
        gap: 5px;
    }
    .notif-card__time i { font-size: 11px; }

    .notif-card__actions {
        gap: 8px;
        width: 100%;
        flex-direction: column;
        align-items: stretch;
    }
    .notif-card__actions .btn,
    .notif-card__actions form,
    .notif-card__actions .inline-form {
        width: 100%;
        justify-content: center;
    }
    .notif-card__actions .inline-form .btn { width: 100%; }

    /* ✅ Badge notification lue iOS */
    .notif-card__done {
        display: flex;
        justify-content: center;
        padding: 12px 16px;
        border-radius: 12px;
        background: #EFEFF4;
        border: none;
        font-size: 14px;
        color: var(--ios-label-3);
        font-weight: 500;
    }
    .notif-card__done i {
        font-size: 15px;
        color: var(--c-success);
    }

    /* Boutons iOS */
    .btn {
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .btn-rust {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .btn-ghost {
        background: var(--ios-card);
        border: 1px solid #E5E5EA;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #EFEFF4; }

    /* ─── EMPTY iOS ─── */
    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        width: 68px;
        height: 68px;
        background: #EFEFF4;
        color: var(--ios-label-3);
        font-size: 28px;
    }
    .empty-state h3 {
        font-size: 17px;
        letter-spacing: -.3px;
    }
    .empty-state p {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }

    /* ─── Pagination iOS ─── */
    .pagination-wrapper { margin-top: 20px; }
    .pagination-list {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 6px;
        border-radius: 14px;
    }
    .pagination-link {
        min-width: 36px;
        height: 36px;
        border-radius: 10px;
        font-size: 13.5px;
    }
    .pagination-item.is-active .pagination-link {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
}

@media (max-width: 480px) {
    .notif-stat__value { font-size: 20px; }
    .notif-stat__label { font-size: 10.5px; }

    .notif-card { padding: 14px; }
    .notif-card__icon {
        top: 14px;
        left: 14px;
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    .notif-card__body { padding-left: 54px; }
    .notif-card__title { font-size: 14.5px; }
    .notif-card__message { font-size: 13px; }

    .notif-mark-all__btn {
        padding: 13px 16px;
        font-size: 13.5px;
    }

    .pagination-link {
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        font-size: 12.5px;
    }
    .pagination-label { display: none; }
    .pagination-info__text { font-size: 11.5px; }
}
</style>
@endpush