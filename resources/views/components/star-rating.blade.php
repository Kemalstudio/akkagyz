@props(['rating' => 0, 'size' => 14, 'gap' => 2])
@php($r = max(0, min(5, (float) $rating)))
<span class="star-rating" style="gap:{{ $gap }}px;">
    @for ($i = 1; $i <= 5; $i++)
        @php($fill = max(0, min(1, $r - ($i - 1))))
        <span class="star-rating-item" style="width:{{ $size }}px;height:{{ $size }}px;">
            <x-icon name="star-outline" :size="$size" class="star-rating-base" />
            <span class="star-rating-fill" style="width:{{ $fill * 100 }}%;">
                <x-icon name="star" :size="$size" />
            </span>
        </span>
    @endfor
</span>
<style>
.star-rating{display:inline-flex;align-items:center;color:var(--warning);}
.star-rating-item{position:relative;display:inline-block;flex-shrink:0;}
.star-rating-base{position:absolute;inset:0;opacity:.32;}
.star-rating-fill{position:absolute;inset:0;overflow:hidden;display:block;}
.star-rating-fill svg{display:block;}
</style>
