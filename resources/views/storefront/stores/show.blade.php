@php
    $reportReasons = ['Мошенничество', 'Не тот товар / брак', 'Не отвечает', 'Оскорбительное поведение', 'Другое'];
@endphp
<x-marketplace-layout :title="$seller->store_name">
    <div class="wrap" style="padding:24px;">
        <div class="store-hero" style="border-radius:22px;background:linear-gradient(115deg,color-mix(in oklch,var(--accent-soft) 55%,var(--surface)),var(--surface) 65%);border:1px solid var(--border);padding:34px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;overflow:hidden;position:relative">
            <div style="width:88px;height:88px;border-radius:22px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:28px;flex-shrink:0;">
                {{ Str::of($seller->store_name)->substr(0, 2)->upper() }}
            </div>
            <div style="flex:1;min-width:240px;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h1 style="font-size:28px;font-weight:700;letter-spacing:-.02em;margin:0">{{ $seller->store_name }}</h1>
                    @if($seller->is_vip)
                        <span class="badge" style="background:var(--warning-soft);color:var(--warning);"><x-icon name="star" :size="12" />VIP</span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:16px;margin-top:8px;flex-wrap:wrap;font-size:13px;color:var(--text-muted);font-weight:600;">
                    @if($seller->store_rating)
                        <span style="display:flex;align-items:center;gap:6px;"><x-star-rating :rating="$seller->store_rating" :size="14" /> {{ $seller->store_rating }} ({{ $reviews->count() }})</span>
                    @endif
                    <span style="display:flex;align-items:center;gap:5px;"><x-icon name="package" :size="14" />{{ $products->total() }} товаров</span>
                    @if($seller->store_address)
                        <span style="display:flex;align-items:center;gap:5px;"><x-icon name="store" :size="14" />{{ $seller->store_address }}</span>
                    @endif
                    @if($seller->phone)
                        <span style="display:flex;align-items:center;gap:5px;">{{ $seller->phone }}</span>
                    @endif
                    <span>На площадке с {{ $seller->created_at->format('Y') }}</span>
                </div>
                @if($seller->store_description)
                    <p style="font-size:14px;color:var(--text-muted);margin-top:12px;max-width:640px;line-height:1.6;">{{ $seller->store_description }}</p>
                @endif
            </div>
            @auth
                <button onclick="document.getElementById('report-form').classList.toggle('open-block')" class="btn-ghost" style="flex-shrink:0;"><x-icon name="bell" :size="14" />Пожаловаться</button>
            @endauth
        </div>

        <div id="report-form" class="report-collapse" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">
            <form method="POST" action="{{ route('stores.report', $seller->store_slug) }}" style="margin-top:16px;padding:20px;border-radius:16px;background:var(--surface);border:1px solid var(--border);display:flex;flex-direction:column;gap:12px;">
                @csrf
                <div style="font-size:14px;font-weight:800;">Пожаловаться на магазин</div>
                <select name="reason" class="input" required style="font-family:var(--font);">
                    <option value="">Выберите причину</option>
                    @foreach($reportReasons as $reason)
                        <option value="{{ $reason }}">{{ $reason }}</option>
                    @endforeach
                </select>
                <textarea name="comment" class="input" style="height:70px;padding:12px 14px;resize:none;" placeholder="Опишите проблему подробнее"></textarea>
                <button type="submit" class="btn-accent" style="width:fit-content;">Отправить жалобу</button>
            </form>
        </div>
        <style>.report-collapse.open-block{max-height:400px;}</style>
    </div>

    <div class="wrap" style="padding:16px 24px 8px;">
        <div style="font-size:21px;font-weight:700;margin-bottom:20px;">Товары магазина</div>
        @if($products->isEmpty())
            <div style="color:var(--text-faint);font-size:14px;padding:20px 0;">У этого магазина пока нет опубликованных товаров.</div>
        @else
            <div class="store-products-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
                @foreach($products as $product)
                    <x-product-card :product="$product" :show-seller="true" />
                @endforeach
            </div>
            <div style="margin-top:24px;">{{ $products->links() }}</div>
        @endif
    </div>

    <div class="wrap" style="padding:44px 24px 56px;max-width:900px;">
        <div style="font-size:21px;font-weight:700;margin-bottom:20px;">Отзывы о магазине <span style="font-weight:500;color:var(--text-faint)">{{ $reviews->count() }}</span></div>

        @auth
            <form method="POST" action="{{ route('stores.reviews.store', $seller->store_slug) }}" style="padding:20px;border-radius:14px;background:var(--surface);border:1px solid var(--border);margin-bottom:24px;">
                @csrf
                <div class="label">{{ $userReview ? 'Изменить отзыв' : 'Оставить отзыв о магазине' }}</div>
                <div style="display:flex;gap:6px;margin-bottom:12px;">
                    @for($i = 1; $i <= 5; $i++)
                        <label style="cursor:pointer;">
                            <input type="radio" name="rating" value="{{ $i }}" {{ ($userReview?->rating ?? 0) == $i ? 'checked' : '' }} style="display:none;" required>
                            <span style="color:var(--warning);"><x-icon :name="($userReview && $i <= $userReview->rating) ? 'star' : 'star-outline'" :size="20" /></span>
                        </label>
                    @endfor
                </div>
                <textarea name="comment" class="input" style="height:70px;padding:12px 14px;resize:none;" placeholder="Как вам магазин?">{{ $userReview->comment ?? '' }}</textarea>
                <button type="submit" class="btn-accent" style="margin-top:12px;">Отправить</button>
            </form>
        @endauth

        <div style="display:flex;flex-direction:column;gap:20px;">
            @forelse($reviews as $review)
                <div style="display:flex;gap:14px;">
                    <div style="width:38px;height:38px;border-radius:50%;background:var(--surface);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;flex-shrink:0;">
                        {{ Str::of($review->user->name)->substr(0, 2)->upper() }}
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span style="font-weight:800;font-size:14px;">{{ $review->user->name }}</span>
                            <x-star-rating :rating="$review->rating" :size="12" />
                            <span style="font-size:12px;color:var(--text-faint);">{{ $review->created_at->format('d.m.Y') }}</span>
                        </div>
                        @if($review->comment)
                            <div style="font-size:14px;color:var(--text-muted);margin-top:6px;line-height:1.6;">{{ $review->comment }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="color:var(--text-faint);font-size:14px;">Пока нет отзывов о магазине.</div>
            @endforelse
        </div>
    </div>
<style>@media(max-width:1000px){.store-products-grid{grid-template-columns:repeat(3,1fr)!important}}@media(max-width:760px){.store-products-grid{grid-template-columns:repeat(2,1fr)!important}.store-hero{padding:24px!important}}@media(max-width:480px){.store-products-grid{grid-template-columns:1fr!important}.store-hero{padding:20px!important}}</style>
</x-marketplace-layout>
