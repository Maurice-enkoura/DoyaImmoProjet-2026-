@extends('layouts.dashboard-agence')

@section('title', 'Historique — DoyaImmo')
@section('page_title', 'Historique')
@section('page_sub', 'Toutes vos actions récentes sur DoyaImmo')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Historique d'activité</h2>
            <p>Toutes vos actions récentes sur DoyaImmo</p>
        </div>
    </div>

    @if($activites->count() > 0)
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px 24px;">
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($activites as $event)
                    <div style="display:flex;gap:16px;padding:12px 0;border-bottom:1px solid var(--border);">
                        <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                    background:{{ $event['statut_class'] === 'success' ? 'var(--green-soft)' : 
                                            ($event['statut_class'] === 'warning' ? 'var(--gold-soft)' : 
                                            ($event['statut_class'] === 'info' ? 'var(--teal-soft)' : 'var(--border)')) }};">
                            <i class="{{ $event['type'] === 'proposition' ? 'fa-solid fa-paper-plane' : 
                                      ($event['type'] === 'rendezvous' ? 'fa-solid fa-calendar-days' : 'fa-solid fa-star') }}"
                               style="color:{{ $event['statut_class'] === 'success' ? 'var(--green)' : 
                                      ($event['statut_class'] === 'warning' ? '#8A6414' : 
                                      ($event['statut_class'] === 'info' ? 'var(--teal)' : 'var(--muted)')) }};">
                            </i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:600;font-size:14px;">{{ $event['titre'] }}</div>
                            <div style="font-size:13px;color:var(--text-soft);">{{ $event['description'] }}</div>
                            <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-calendar"></i> {{ $event['date']->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                        @if(isset($event['statut']))
                            <div style="flex-shrink:0;">
                                <span class="status-pill status-{{ $event['statut_class'] ?? 'default' }}">
                                    {{ $event['statut'] }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div style="margin-top:30px;">
            {{ $activites->links() }}
        </div>
    @else
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-solid fa-clock" style="font-size:40px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;">Aucune activité pour le moment.</p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-default {
        background: #F7F9FC;
        color: var(--text-soft);
    }
    .status-success {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-warning {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-info {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-danger {
        background: #FFEBEE;
        color: #C62828;
    }
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
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
</style>
@endpush