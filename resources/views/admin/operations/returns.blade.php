@php($statusLabels=['pending'=>'Ожидает','approved'=>'Одобрено','rejected'=>'Отклонено','refunded'=>'Возвращены средства'])
<x-dashboard-layout title="Возвраты" active="returns"><div class="page-head"><div><div class="page-title">Возвраты и споры</div><div class="page-subtitle">Решения по доставленным товарам и возвратам средств</div></div></div><div class="card table-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Заявка</th><th>Заказ</th><th>Покупатель</th><th>Причина</th><th>Статус</th><th>Решение</th></tr></thead><tbody>@forelse($returns as $r)<tr><td><strong>{{ $r->number }}</strong></td><td>{{ $r->orderItem->order->number }}<div class="entity-meta">{{ $r->orderItem->product_name }}</div></td><td>{{ $r->user->name }}</td><td>{{ $r->reason }}</td><td>{{ $statusLabels[$r->status] ?? $r->status }}</td><td>
@if($r->status==='pending')
<form method="POST" action="{{ route('admin.returns.resolve',$r) }}">@csrf @method('PATCH')<select class="admin-input" name="status"><option value="approved">Одобрить</option><option value="rejected">Отклонить</option><option value="refunded">Средства возвращены</option></select><input class="admin-input" name="refund_amount" type="number" min="0" placeholder="Сумма"><input class="admin-input" name="resolution" required placeholder="Комментарий"><button class="btn-accent">Сохранить</button></form>
@else
<div class="entity-meta">{{ $statusLabels[$r->status] ?? $r->status }} · {{ $r->resolver?->name ?: 'система' }} · {{ $r->resolved_at?->format('d.m.Y H:i') }}</div>
@if($r->refund_amount)<div class="entity-meta">Сумма: {{ number_format($r->refund_amount,0,'',' ') }}</div>@endif
<div>{{ $r->resolution }}</div>
@endif
</td></tr>@empty<tr><td colspan="6" class="empty-state">Заявок нет</td></tr>@endforelse</tbody></table></div><div class="pagination-wrap">{{ $returns->links() }}</div></div></x-dashboard-layout>
