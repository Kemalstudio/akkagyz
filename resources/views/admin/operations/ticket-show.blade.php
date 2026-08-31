@php($priorities=['low'=>'Низкий','normal'=>'Обычный','high'=>'Высокий','urgent'=>'Срочный'])
@php($statuses=['open'=>'Открыт','in_progress'=>'В работе','answered'=>'Отвечен','closed'=>'Закрыт'])
<x-dashboard-layout title="Тикет {{ $ticket->number }}" active="support">
<div class="page-head">
    <div>
        <a class="back-link" href="{{ route('admin.support.index') }}">← Все обращения</a>
        <div class="page-title">{{ $ticket->number }}</div>
        <div class="page-subtitle">{{ $ticket->subject }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:18px">
    <div style="display:grid;gap:18px">
        <section class="card" style="padding:22px">
            <h3>Переписка</h3>
            @forelse($ticket->messages->sortBy('created_at') as $m)
                <div style="padding:13px 0;border-top:1px solid var(--border)">
                    <strong>{{ $m->user?->name ?: 'Пользователь' }}{{ $m->user?->isAdmin() ? ' (админ)' : '' }}</strong>
                    <div class="entity-meta">{{ $m->created_at->format('d.m.Y H:i') }}</div>
                    <p style="margin-top:6px;white-space:pre-wrap">{{ $m->message }}</p>
                </div>
            @empty
                <div class="empty-state">Сообщений пока нет</div>
            @endforelse
        </section>

        <form class="card" style="padding:22px" method="POST" action="{{ route('admin.support.reply',$ticket) }}">
            @csrf
            <h3>Ответить</h3>
            @if($errors->any())<div class="alert alert-danger" style="margin:12px 0">@foreach($errors->all() as $error){{ $error }}<br>@endforeach</div>@endif
            <textarea class="input" name="message" rows="4" required placeholder="Текст ответа покупателю">{{ old('message') }}</textarea>
            <button class="btn-accent" style="margin-top:12px">Отправить ответ</button>
        </form>
    </div>

    <div style="display:grid;gap:18px">
        <form class="card" style="padding:22px" method="POST" action="{{ route('admin.support.update',$ticket) }}">
            @csrf @method('PATCH')
            <h3>Управление</h3>
            <label>Статус</label>
            <select class="input" name="status">
                @foreach($statuses as $v=>$l)<option value="{{ $v }}" @selected($ticket->status===$v)>{{ $l }}</option>@endforeach
            </select>
            <label>Приоритет</label>
            <select class="input" name="priority">
                @foreach($priorities as $v=>$l)<option value="{{ $v }}" @selected($ticket->priority===$v)>{{ $l }}</option>@endforeach
            </select>
            <label>Ответственный</label>
            <select class="input" name="assigned_to">
                <option value="">Не назначен</option>
                @foreach($admins as $a)<option value="{{ $a->id }}" @selected($ticket->assigned_to===$a->id)>{{ $a->name }}</option>@endforeach
            </select>
            <button class="btn-accent" style="width:100%;margin-top:14px">Сохранить</button>
        </form>

        <section class="card" style="padding:22px">
            <h3>Обращение</h3>
            <div class="entity-meta" style="margin-top:8px">Покупатель</div>
            <div>{{ $ticket->user?->name }}</div>
            <div class="entity-meta" style="margin-top:8px">Заказ</div>
            <div>{{ $ticket->order?->number ?: '—' }}</div>
            <div class="entity-meta" style="margin-top:8px">Создан</div>
            <div>{{ $ticket->created_at->format('d.m.Y H:i') }}</div>
            @if($ticket->closed_at)
                <div class="entity-meta" style="margin-top:8px">Закрыт</div>
                <div>{{ $ticket->closed_at->format('d.m.Y H:i') }}</div>
            @endif
        </section>
    </div>
</div>
<style>label{display:block;margin:12px 0 6px;font-size:12px}.input{padding:10px}@media(max-width:850px){.admin-content>div[style*="grid-template-columns"]{grid-template-columns:1fr!important}}</style>
</x-dashboard-layout>
