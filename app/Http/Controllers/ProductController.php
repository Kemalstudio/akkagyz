<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Services\ProductAttributeFilterService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ProductAttributeFilterService $attributeFilters)
    {
        $query = Product::query()->active()->own()->with(['category', 'seller', 'images']);

        if ($request->filled('q')) {
            $query->search($request->string('q'));
        }

        $category = null;
        if ($request->filled('category')) {
            $category = Category::with(['children','parent.children'])->where('slug', $request->string('category'))->first();
            if ($category) {
                $query->whereIn('category_id', $category->idsWithChildren());
            }
        }

        $categoryRoot = $category?->root();
        $priceCeiling = (int) ((clone $query)->max('price') ?? 0);

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->boolean('on_sale')) $query->whereNotNull('compare_price')->whereColumn('compare_price', '>', 'price');
        if ($request->boolean('vip')) $query->where('is_vip', true);

        $categoryAttributes = $attributeFilters->attributesFor($category);
        $attributeFacets = $attributeFilters->facets($query, $categoryAttributes);
        $attributeFilters->applySelected($query, $request, $categoryAttributes);

        match ($request->string('sort')->value()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            'rating' => $query->orderByDesc('rating_avg'),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $request->filled('q')
                ? $query->orderByRelevance($request->string('q'))->orderByDesc('sales_count')
                : $query->orderByDesc('sales_count'),
        };

        $perPage = in_array((int) $request->input('per_page'), [30, 50, 100, 200, 300], true)
            ? (int) $request->input('per_page')
            : 30;

        $products = $query->paginate($perPage)->withQueryString();

        if ($request->boolean('partial')) {
            return response()->json([
                'html' => view('storefront.partials.product-cards', ['products' => $products])->render(),
                'nextPageUrl' => $products->nextPageUrl(),
            ]);
        }

        return view('storefront.catalog', [
            'products' => $products,
            'categories' => Category::orderBy('sort_order')->get(),
            'activeCategory' => $category,
            'categoryRoot' => $categoryRoot,
            'priceCeiling' => $priceCeiling,
            'filterContext' => $this->filterContext($categoryRoot?->slug),
            'attributeFacets' => $attributeFacets,
        ]);
    }

    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['products' => [], 'categories' => []]);
        }

        $products = Product::active()
            ->own()
            ->search($q)
            ->orderByRelevance($q)
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get(['name', 'slug', 'price']);

        $categories = Category::where('name', 'like', "%{$q}%")->limit(3)->get(['name', 'slug']);

        return response()->json(['products' => $products, 'categories' => $categories]);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->own()->with(['category', 'seller', 'images', 'attributeValues.attribute'])
            ->withCount('reviews')
            ->firstOrFail();

        $reviews = $product->reviews()->published()->with(['user', 'replies.user'])->latest()->limit(20)->get();

        $related = Product::active()
            ->own()
            ->with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('storefront.product', compact('product', 'reviews', 'related'));
    }

    public function storeReview(Request $request, Product $product)
    {
        $verified = \App\Models\OrderItem::where('product_id',$product->id)->where('status','delivered')->whereHas('order',fn($q)=>$q->where('user_id',$request->user()->id))->exists();
        abort_unless($verified, 403, 'Отзыв можно оставить только после доставки товара.');
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $request->user()->id],
            $data + ['is_verified_purchase'=>true,'status'=>'published']
        );

        $product->rating_avg = $product->reviews()->published()->avg('rating') ?? 0;
        $product->rating_count = $product->reviews()->published()->count();
        $product->save();

        return back()->with('status', 'Спасибо за отзыв!');
    }

    private function filterContext(?string $slug): array
    {
        return match($slug) {
            'elektronika' => ['title'=>'Параметры электроники','hint'=>'Цена, рейтинг и наличие','quick'=>[['До 5 000',0,5000],['5 000–15 000',5000,15000],['От 15 000',15000,null]]],
            'ofis-i-biznes' => ['title'=>'Для офиса и бизнеса','hint'=>'Подберите оснащение по бюджету','quick'=>[['До 2 500',0,2500],['2 500–7 500',2500,7500],['Премиум',7500,null]]],
            'kancelyariya' => ['title'=>'Канцелярские товары','hint'=>'Фильтры по стоимости и наличию','quick'=>[['До 2 000',0,2000],['2 000–5 000',2000,5000],['От 5 000',5000,null]]],
            'tvorchestvo' => ['title'=>'Товары для творчества','hint'=>'Найдите материалы под свой проект','quick'=>[['До 3 000',0,3000],['3 000–7 000',3000,7000],['Профессиональные',7000,null]]],
            default => ['title'=>'Умные фильтры','hint'=>'Цена, рейтинг и наличие','quick'=>[['До 3 000',0,3000],['3 000–10 000',3000,10000],['От 10 000',10000,null]]],
        };
    }
}
