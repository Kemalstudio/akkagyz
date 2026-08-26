<x-layout title="Избранное">
    <div class="wrap" style="padding:32px 24px 64px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap">
            <div style="font-size:28px;font-weight:700;display:flex;align-items:center;gap:12px;letter-spacing:-.02em">
                <span style="color:var(--accent);"><x-icon name="heart-fill" :size="26" /></span>
                Избранное <span style="color:var(--text-faint);font-weight:700;font-size:18px;">&middot; {{ $items->count() }} товаров</span>
            </div>
            @if($items->isNotEmpty())<form method="POST" action="{{ route('wishlist.clear') }}" onsubmit="return confirm('Очистить избранное?')">@csrf @method('DELETE')<button class="btn-ghost" style="color:var(--danger)"><x-icon name="trash" :size="14"/>Очистить</button></form>@endif
        </div>

        @if($items->isEmpty())
            <div style="padding:60px 0;text-align:center;">
                <div style="color:var(--text-faint);font-size:15px;font-weight:600;margin-bottom:16px;">В избранном пока ничего нет</div>
                <a href="{{ route('catalog') }}" class="btn-accent" style="display:inline-flex;">Перейти в каталог</a>
            </div>
        @else
            <div class="wishlist-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
                @foreach($items as $item)
                    <x-product-card :product="$item->product" />
                @endforeach
            </div>
        @endif
    </div>
    <style>@media(max-width:1050px){.wishlist-grid{grid-template-columns:repeat(3,1fr)!important}}@media(max-width:760px){.wishlist-grid{grid-template-columns:repeat(2,1fr)!important}}@media(max-width:480px){.wishlist-grid{grid-template-columns:1fr!important}}</style>
</x-layout>
