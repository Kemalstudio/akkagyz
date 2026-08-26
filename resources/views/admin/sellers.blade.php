@php
    $statusLabels = ['pending' => 'На рассмотрении', 'approved' => 'Одобрен', 'rejected' => 'Отклонён'];
    $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
@endphp
<x-dashboard-layout title="Продавцы" active="sellers">
    <div style="display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
        <div><div class="page-title">Продавцы</div><div class="page-subtitle">Проверяйте заявки, магазины и жалобы покупателей</div></div>
        <form method="GET" class="toolbar"><input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Магазин или владелец"><select class="admin-input" name="status"><option value="">Все статусы</option>@foreach($statusLabels as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select><button class="btn-accent">Найти</button></form>
    </div>

    @if($reports->isNotEmpty())
        <div class="card" style="padding:24px;margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <span style="font-size:15px;font-weight:800;">Жалобы на магазины</span>
                <span class="badge" style="background:var(--danger-soft);color:var(--danger);">{{ $reports->count() }} новых</span>
            </div>
            @foreach($reports as $report)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:700;">{{ $report->reason }} &mdash; {{ $report->seller->store_name }}</div>
                        <div style="font-size:12px;color:var(--text-faint);margin-top:2px;">{{ $report->comment ?: 'Без комментария' }} &middot; от {{ $report->user->name }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">@csrf<button type="submit" style="height:32px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:1px solid var(--border);background:none;color:var(--text);">Рассмотрено</button></form>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card" style="padding:24px;">
        <div class="admin-table-wrap"><table class="admin-table">
            <thead><tr><th>Магазин</th><th>Владелец</th><th>Товаров</th><th>Статус</th><th></th></tr></thead>
            <tbody>
                @foreach($sellers as $seller)
                <tr>
                    <td style="display:flex;align-items:center;gap:10px;font-weight:700;"><div class="avatar">{{ Str::of($seller->store_name ?? $seller->name)->substr(0, 2)->upper() }}</div>{{ $seller->store_name ?? '—' }}</td>
                    <td>{{ $seller->name }}</td>
                    <td>{{ $seller->products_count }}</td>
                    <td><span class="badge" style="background:var(--{{ $statusColors[$seller->store_status] ?? 'warning' }}-soft);color:var(--{{ $statusColors[$seller->store_status] ?? 'warning' }});">{{ $statusLabels[$seller->store_status] ?? '—' }}</span></td>
                    <td style="display:flex;gap:8px;">
                        @if($seller->store_slug)<a href="{{ route('stores.show',$seller->store_slug) }}" target="_blank" class="action-btn"><x-icon name="arrow-right" :size="13"/>Открыть</a>@endif
                        @if($seller->store_status !== 'approved')
                            <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}">@csrf<button type="submit" style="height:32px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:none;background:var(--success-soft);color:var(--success);">Одобрить</button></form>
                        @endif
                        @if($seller->store_status !== 'rejected')
                            <form method="POST" action="{{ route('admin.sellers.reject', $seller) }}">@csrf<button type="submit" style="height:32px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:none;background:var(--danger-soft);color:var(--danger);">Отклонить</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.sellers.block', $seller) }}">@csrf<button type="submit" style="height:32px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:1px solid var(--border);background:none;color:var(--text);">{{ $seller->is_blocked ? 'Разблокировать' : 'Заблокировать' }}</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        <div style="margin-top:20px;">{{ $sellers->links() }}</div>
    </div>
</x-dashboard-layout>
