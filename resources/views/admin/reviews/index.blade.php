@php
    $statusLabels = ['published' => 'Опубликован', 'hidden' => 'Скрыт', 'rejected' => 'Отклонён'];
    $statusColors = ['published' => 'success', 'hidden' => 'warning', 'rejected' => 'danger'];
    $sortLabels = ['latest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'rating_desc' => 'Оценка: по убыванию', 'rating_asc' => 'Оценка: по возрастанию', 'reports_desc' => 'По числу жалоб'];
    $hasFilters = collect(request()->only(['search', 'status', 'rating', 'reported']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Отзывы" active="reviews">
<style>
.reviews-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.reviews-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.reviews-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.chip-check{display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:var(--text-muted);cursor:pointer}
.chip-check input{accent-color:var(--danger)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.moderate-form{display:flex;gap:6px;flex-wrap:wrap;align-items:center;min-width:280px}
.moderate-form .admin-input{height:34px;font-size:11px}
.moderate-form select{width:120px}
.moderate-form input{flex:1;min-width:110px}
@media(max-width:1200px){.reviews-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.reviews-stats{grid-template-columns:1fr}}
.reply-toggle-btn{position:relative}
.reply-toggle-btn.has-replies{color:var(--accent);border-color:color-mix(in oklch,var(--accent) 35%,var(--border))}
.reply-panel{background:var(--bg-elevated)}
.reply-panel-inner{padding:16px 22px;max-width:640px}
.reply-thread{display:grid;gap:10px;margin-bottom:14px}
.reply-item{display:flex;gap:10px;padding:10px 12px;background:var(--surface);border:1px solid var(--border);border-radius:12px}
.reply-item-avatar{width:30px;height:30px;border-radius:9px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;flex-shrink:0}
.reply-item-head{display:flex;align-items:center;gap:8px}
.reply-item-head strong{font-size:12.5px;font-weight:800}
.reply-item-head time{font-size:10px;color:var(--text-faint)}
.reply-item-body{font-size:12.5px;color:var(--text-muted);margin-top:4px;line-height:1.5}
.reply-store-badge{font-size:9px;padding:2px 6px;border-radius:6px;background:var(--accent);color:#fff;font-weight:800}
.reply-item-delete{border:0;background:none;color:var(--text-faint);font-size:16px;padding:0 4px;margin-left:auto;align-self:flex-start}
.reply-item-delete:hover{color:var(--danger)}
.reply-compose{display:flex;gap:8px;align-items:flex-end}
.reply-compose textarea{flex:1;min-height:52px;resize:vertical;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text);padding:10px 12px;font:inherit;font-size:12.5px;outline:none}
.reply-compose textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
@media(max-width:600px){.reply-compose{flex-direction:column;align-items:stretch}}
</style>

<div class="page-head">
    <div><div class="page-title">Модерация отзывов</div><div class="page-subtitle">Проверенные покупки, жалобы и скрытый контент</div></div>
</div>

<div class="reviews-stats">
    @foreach([
        ['Всего отзывов', number_format($overview['total'], 0, '', ' '), 'star', 'accent', 'За всё время', route('admin.reviews.index')],
        ['Опубликованы', number_format($overview['published'], 0, '', ' '), 'check', 'success', 'Видны покупателям', route('admin.reviews.index', ['status' => 'published'])],
        ['Скрыты или отклонены', number_format($overview['flagged'], 0, '', ' '), 'x', 'warning', 'Требуют внимания', route('admin.reviews.index', ['status' => 'flagged'])],
        ['Жалобы в очереди', number_format($overview['reported'], 0, '', ' '), 'bell', 'danger', 'Ждут решения', route('admin.reviews.index', ['reported' => 1])],
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

<form method="GET" class="card filter-card">
    <div class="filter-grid">
        <div class="filter-field" style="grid-column:span 2">
            <label>Поиск</label>
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Товар, автор или текст отзыва">
        </div>
        <div class="filter-field">
            <label>Статус</label>
            <select class="admin-input" name="status">
                <option value="">Все статусы</option>
                <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Скрыты или отклонены</option>
                @foreach($statusLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Оценка</label>
            <select class="admin-input" name="rating">
                <option value="">Любая оценка</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} из 5</option>
                @endfor
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
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} отзывов</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        <label class="chip-check"><input type="checkbox" name="reported" value="1" {{ request()->boolean('reported') ? 'checked' : '' }}><x-icon name="bell" :size="13" />Только с жалобами</label>
        <div style="display:flex;gap:10px">
            @if($hasFilters)<a href="{{ route('admin.reviews.index') }}" class="btn-ghost">Сбросить</a>@endif
            <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
        </div>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $reviews->firstItem() ?? 0 }}–{{ $reviews->lastItem() ?? 0 }} из {{ number_format($reviews->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Товар</th><th>Автор</th><th>Оценка</th><th>Отзыв</th><th>Жалобы</th><th>Статус</th><th>Решение</th><th>Ответ</th></tr></thead>
        <tbody>
            @forelse($reviews as $r)
            <tr>
                <td class="entity-name">{{ $r->product->name }}</td>
                <td>{{ $r->user->name }}<div class="entity-meta">{{ $r->is_verified_purchase ? 'Покупка подтверждена' : 'Не подтверждена' }}</div></td>
                <td><span class="rating-chip"><x-icon name="star" :size="12"/>{{ $r->rating }}/5</span></td>
                <td style="max-width:260px">{{ Str::limit($r->comment, 100) ?: '—' }}</td>
                <td>@if($r->reports_count)<span class="badge" style="background:var(--danger-soft);color:var(--danger)">{{ $r->reports_count }}</span>@else<span class="entity-meta">0</span>@endif</td>
                <td><span class="badge" style="background:var(--{{ $statusColors[$r->status] ?? 'accent' }}-soft);color:var(--{{ $statusColors[$r->status] ?? 'accent' }})">{{ $statusLabels[$r->status] ?? $r->status }}</span></td>
                <td>
                    <form method="POST" action="{{ route('admin.reviews.moderate', $r) }}" class="moderate-form" onsubmit="return reviewStatusConfirm(this)">
                        @csrf @method('PATCH')
                        <select class="admin-input" name="status">
                            @foreach($statusLabels as $val => $label)
                                <option value="{{ $val }}" {{ $r->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input class="admin-input" name="moderation_reason" value="{{ $r->moderation_reason }}" placeholder="Причина (необязательно)">
                        <button type="submit" class="btn-accent" style="height:34px;padding:0 12px;font-size:12px">Сохранить</button>
                    </form>
                </td>
                <td>
                    <button type="button" class="action-btn reply-toggle-btn {{ $r->replies->isNotEmpty() ? 'has-replies' : '' }}" onclick="toggleReplyRow({{ $r->id }})">
                        <x-icon name="mail" :size="13"/>{{ $r->replies->isNotEmpty() ? 'Ответы ('.$r->replies->count().')' : 'Ответить' }}
                    </button>
                </td>
            </tr>
            <tr class="reply-panel" id="reply-row-{{ $r->id }}" style="display:none">
                <td colspan="8" style="padding:0">
                    <div class="reply-panel-inner">
                        @if($r->replies->isNotEmpty())
                        <div class="reply-thread">
                            @foreach($r->replies as $reply)
                            <div class="reply-item">
                                <div class="reply-item-avatar">{{ Str::of($reply->user->name)->substr(0,2)->upper() }}</div>
                                <div style="flex:1;min-width:0">
                                    <div class="reply-item-head">
                                        <strong>{{ $reply->user->name }}</strong>
                                        @if($reply->user->isAdmin())<span class="reply-store-badge">{{ $businessSettings->site_name }}</span>@endif
                                        <time>{{ $reply->created_at->diffForHumans() }}</time>
                                    </div>
                                    <div class="reply-item-body">{{ $reply->message }}</div>
                                </div>
                                <form method="POST" action="{{ route('reviews.replies.destroy', $reply) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="reply-item-delete" title="Удалить">×</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        <form method="POST" action="{{ route('reviews.replies.store', $r) }}" class="reply-compose">
                            @csrf
                            <textarea name="message" maxlength="1500" required placeholder="Ответ от {{ $businessSettings->site_name }}..."></textarea>
                            <button type="submit" class="btn-accent" style="height:38px;padding:0 16px;font-size:12.5px">Отправить</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty<tr><td colspan="8" class="empty-state">Отзывы не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $reviews->links() }}</div>
</div>

<script>
function toggleReplyRow(id) {
    var row = document.getElementById('reply-row-' + id);
    if (!row) return;
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
function reviewStatusConfirm(form) {
    var value = form.status.value;
    if (value === 'hidden' || value === 'rejected') {
        return confirm('Изменить статус отзыва на «' + form.status.options[form.status.selectedIndex].text + '»?');
    }
    return true;
}
</script>
</x-dashboard-layout>
