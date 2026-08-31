@php
    $statusLabels = ['pending' => 'Ожидает', 'paid' => 'Оплачена'];
    $statusColors = ['pending' => 'warning', 'paid' => 'success'];
    $sortLabels = ['latest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'amount_desc' => 'Сумма: по убыванию', 'amount_asc' => 'Сумма: по возрастанию'];
    $hasFilters = collect(request()->only(['seller_id', 'status']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Выплаты" active="payouts">
<style>
.payouts-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.payouts-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.payouts-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.payout-create-card{padding:20px;margin-bottom:20px}
.payout-create-card .filter-field small{display:block;margin-top:6px;font-size:11px;color:var(--text-faint);font-weight:700}
.complete-form{display:flex;gap:6px;align-items:center}
.complete-form .admin-input{width:130px;height:32px;font-size:11px}
@media(max-width:1200px){.payouts-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.payouts-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Выплаты продавцам</div><div class="page-subtitle">Создание и подтверждение выплат из доступного баланса</div></div>
    <a href="{{ route('admin.finance.index') }}" class="btn-ghost"><x-icon name="chart" :size="15"/>Финансы</a>
</div>

<div class="payouts-stats">
    @foreach([
        ['Ожидают выплаты', number_format($overview['pendingCount'], 0, '', ' '), 'package', 'warning', 'Заявки в очереди', route('admin.payouts.index', ['status' => 'pending'])],
        ['Сумма к выплате', number_format($overview['pendingAmount'], 0, '', ' ').' '.$businessSettings->currency, 'wallet', 'warning', 'Ожидает перевода', route('admin.payouts.index', ['status' => 'pending'])],
        ['Выплачено заявок', number_format($overview['paidCount'], 0, '', ' '), 'check', 'success', 'Завершённые выплаты', route('admin.payouts.index', ['status' => 'paid'])],
        ['Выплачено всего', number_format($overview['paidAmount'], 0, '', ' ').' '.$businessSettings->currency, 'cash', 'success', 'За всё время', route('admin.payouts.index', ['status' => 'paid'])],
    ] as $metric)
    <a href="{{ $metric[5] }}" class="stat stat-link">
        <div style="display:flex;justify-content:space-between;align-items:start">
            <div><div class="kicker">{{ $metric[0] }}</div><div class="metric-value">{{ $metric[1] }}</div></div>
            <div class="stat-icon" style="background:var(--{{ $metric[3] }}-soft);color:var(--{{ $metric[3] }})"><x-icon :name="$metric[2]" :size="17" /></div>
        </div>
        <div class="metric-note">{{ $metric[4] }}<x-icon name="chevron-right" :size="12" class="stat-link-arrow"/></div>
    </a>
    @endforeach
</div>

@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<form class="card payout-create-card" method="POST" action="{{ route('admin.payouts.store') }}">
    @csrf
    <div class="filter-grid">
        <div class="filter-field">
            <label>Продавец</label>
            <select class="admin-input" name="seller_id" id="payout-seller" required>
                <option value="">Выберите продавца</option>
                @foreach($sellers as $s)
                    <option value="{{ $s->id }}" data-balance="{{ $sellerBalances[$s->id] ?? 0 }}" {{ old('seller_id') == $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
                @endforeach
            </select>
            <small id="payout-balance-hint">Выберите продавца, чтобы увидеть баланс</small>
        </div>
        <div class="filter-field">
            <label>Сумма</label>
            <input class="admin-input" type="number" min="1" name="amount" id="payout-amount" required placeholder="0" value="{{ old('amount') }}">
        </div>
        <div class="filter-field">
            <label>Способ</label>
            <select class="admin-input" name="method">
                <option value="bank_transfer">Банковский перевод</option>
                <option value="card">Карта</option>
                <option value="cash">Наличные</option>
            </select>
        </div>
        <div class="filter-field" style="align-self:end">
            <button type="submit" class="btn-accent" style="width:100%"><x-icon name="plus" :size="14"/>Создать выплату</button>
        </div>
    </div>
</form>

<form method="GET" class="card filter-card">
    <div class="filter-grid">
        <div class="filter-field">
            <label>Продавец</label>
            <select class="admin-input" name="seller_id">
                <option value="">Все продавцы</option>
                @foreach($sellers as $s)
                    <option value="{{ $s->id }}" {{ (int) request('seller_id') === $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Статус</label>
            <select class="admin-input" name="status">
                <option value="">Все статусы</option>
                @foreach($statusLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Сортировка</label>
            <select class="admin-input" name="sort">
                @foreach($sortLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('sort', 'latest') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>На странице</label>
            <select class="admin-input" name="per_page">
                @foreach([15, 30, 50] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} выплат</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        @if($hasFilters)<a href="{{ route('admin.payouts.index') }}" class="btn-ghost">Сбросить</a>@endif
        <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $payouts->firstItem() ?? 0 }}–{{ $payouts->lastItem() ?? 0 }} из {{ number_format($payouts->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Номер</th><th>Продавец</th><th>Сумма</th><th>Статус</th><th>Референс</th><th>Дата</th><th></th></tr></thead>
        <tbody>
            @forelse($payouts as $p)
            <tr>
                <td class="entity-name">{{ $p->number }}</td>
                <td><div class="entity-cell">
                    <div class="avatar">{{ Str::of($p->seller->store_name)->substr(0, 2)->upper() }}</div>
                    <div class="entity-name">{{ $p->seller->store_name }}</div>
                </div></td>
                <td style="font-weight:700">{{ number_format($p->amount, 0, '', ' ') }} {{ $businessSettings->currency }}</td>
                <td><span class="badge" style="background:var(--{{ $statusColors[$p->status] ?? 'warning' }}-soft);color:var(--{{ $statusColors[$p->status] ?? 'warning' }})">{{ $statusLabels[$p->status] ?? $p->status }}</span></td>
                <td>{{ $p->reference ?: '—' }}</td>
                <td>{{ $p->created_at->format('d.m.Y') }}</td>
                <td>
                    @if($p->status === 'pending')
                        <form method="POST" action="{{ route('admin.payouts.complete', $p) }}" class="complete-form" onsubmit="return confirm('Отметить выплату {{ $p->number }} оплаченной?');">
                            @csrf @method('PATCH')
                            <input class="admin-input" name="reference" placeholder="№ перевода">
                            <button type="submit" class="action-btn" style="border-color:transparent;background:var(--success-soft);color:var(--success)"><x-icon name="check" :size="13"/>Оплачено</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">Выплаты не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $payouts->links() }}</div>
</div>

<script>
(function () {
    var select = document.getElementById('payout-seller');
    var hint = document.getElementById('payout-balance-hint');
    var amount = document.getElementById('payout-amount');
    function update() {
        var option = select.options[select.selectedIndex];
        var balance = option ? parseInt(option.dataset.balance || '0', 10) : 0;
        if (!option || !option.value) { hint.textContent = 'Выберите продавца, чтобы увидеть баланс'; amount.removeAttribute('max'); return; }
        hint.textContent = 'Доступно: ' + balance.toLocaleString('ru-RU') + ' {{ $businessSettings->currency }}';
        amount.setAttribute('max', balance);
    }
    select.addEventListener('change', update);
    update();
})();
</script>
</x-dashboard-layout>
