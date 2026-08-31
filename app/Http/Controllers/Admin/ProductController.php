<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\User;
use App\Notifications\ProductReviewed;
use App\Support\Concerns\HandlesProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HandlesProductImages;

    public function index(Request $request)
    {
        $statusLabels = [
            'draft' => 'Черновик',
            'pending' => 'На модерации',
            'active' => 'Активен',
            'rejected' => 'Отклонён',
        ];
        $status = in_array($request->input('status'), array_keys($statusLabels), true)
            ? $request->input('status')
            : null;
        $source = in_array($request->input('source'), ['own', 'marketplace'], true)
            ? $request->input('source')
            : null;
        $stock = in_array($request->input('stock'), ['in', 'low', 'out', 'issues'], true)
            ? $request->input('stock')
            : null;
        $sort = in_array($request->input('sort'), ['latest', 'oldest', 'price_desc', 'price_asc', 'stock_asc', 'sales_desc'], true)
            ? $request->input('sort')
            : 'latest';
        $perPage = in_array($request->integer('per_page'), [15, 30, 50], true)
            ? $request->integer('per_page')
            : 15;
        $search = mb_substr(trim((string) $request->input('search')), 0, 120);

        $query = Product::with(['category.parent', 'seller', 'images'])
            ->withCount('variants');

        $query->when($status, fn ($builder) => $builder->where('status', $status));
        $query->when($request->integer('category_id') > 0, fn ($builder) => $builder->where('category_id', $request->integer('category_id')));
        $query->when($request->integer('seller_id') > 0, fn ($builder) => $builder->where('seller_id', $request->integer('seller_id')));
        $query->when($source === 'own', fn ($builder) => $builder->whereNull('seller_id'));
        $query->when($source === 'marketplace', fn ($builder) => $builder->whereNotNull('seller_id'));
        $query->when($request->boolean('vip'), fn ($builder) => $builder->where('is_vip', true));
        $query->when($stock === 'out', fn ($builder) => $builder->where('stock', '<=', 0));
        $query->when($stock === 'low', fn ($builder) => $builder->where('stock', '>', 0)->whereColumn('stock', '<=', 'low_stock_threshold'));
        $query->when($stock === 'in', fn ($builder) => $builder->whereColumn('stock', '>', 'low_stock_threshold'));
        $query->when($stock === 'issues', fn ($builder) => $builder->where(fn ($b) => $b->where('stock', '<=', 0)->orWhere(fn ($b2) => $b2->where('stock', '>', 0)->whereColumn('stock', '<=', 'low_stock_threshold'))));
        $query->when($request->filled('price_from'), fn ($builder) => $builder->where('price', '>=', max(0, $request->integer('price_from'))));
        $query->when($request->filled('price_to'), fn ($builder) => $builder->where('price', '<=', max(0, $request->integer('price_to'))));
        $query->when($search !== '', function ($builder) use ($search) {
            $term = '%'.$search.'%';
            $builder->where(function ($nested) use ($term) {
                $nested->where('name', 'like', $term)
                    ->orWhere('sku', 'like', $term)
                    ->orWhere('barcode', 'like', $term)
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', $term))
                    ->orWhereHas('seller', fn ($seller) => $seller->where('name', 'like', $term)->orWhere('store_name', 'like', $term));
            });
        });

        match ($sort) {
            'oldest' => $query->oldest(),
            'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
            'price_asc' => $query->orderBy('price')->orderByDesc('id'),
            'stock_asc' => $query->orderBy('stock')->orderByDesc('id'),
            'sales_desc' => $query->orderByDesc('sales_count')->orderByDesc('id'),
            default => $query->latest(),
        };

        $products = $query->paginate($perPage)->withQueryString();
        $statusCounts = Product::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $overview = [
            'total' => (int) $statusCounts->sum(),
            'active' => (int) $statusCounts->get('active', 0),
            'pending' => (int) $statusCounts->get('pending', 0),
            'stock_issues' => Product::where('stock', '<=', 0)
                ->orWhere(fn ($builder) => $builder->where('stock', '>', 0)->whereColumn('stock', '<=', 'low_stock_threshold'))
                ->count(),
            'inventory_value' => (int) Product::sum(DB::raw('price * stock')),
        ];
        $categories = Category::with('parent')->orderBy('name')->get(['id', 'parent_id', 'name']);
        $sellers = User::where('role', 'seller')->orderByRaw('COALESCE(store_name, name)')->get(['id', 'name', 'store_name']);

        return view('admin.products', compact(
            'categories',
            'overview',
            'products',
            'sellers',
            'statusCounts',
            'statusLabels',
        ));
    }

    public function create()
    {
        $categories = Category::with('attributes')->orderBy('sort_order')->get();

        return view('admin.products.form', ['categories' => $categories, 'product' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_vip'] = $request->boolean('is_vip');
        $data['condition'] ??= 'new';
        $attributeValues = $data['attribute_values'] ?? [];
        unset($data['attribute_values']);

        $product = Product::create([
            ...$data,
            'seller_id' => null,
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'status' => 'active',
        ]);
        $this->storeProductImages($request, $product);
        $this->syncAttributeValues($product, $attributeValues);

        return redirect()->route('admin.products')->with('status', "Товар «{$product->name}» добавлен и опубликован.");
    }

    public function edit(Product $product)
    {
        abort_unless($product->seller_id === null, 404);
        $product->load(['images', 'attributeValues']);
        $categories = Category::with('attributes')->orderBy('sort_order')->get();

        return view('admin.products.form', compact('categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->seller_id === null, 404);

        $data = $this->validated($request);
        $data['is_vip'] = $request->boolean('is_vip');
        $data['condition'] ??= 'new';
        $attributeValues = $data['attribute_values'] ?? [];
        unset($data['attribute_values']);

        $product->update($data);
        if ($request->hasFile('images')) {
            $this->replaceProductImages($request, $product);
        }
        $this->syncAttributeValues($product, $attributeValues);

        return redirect()->route('admin.products')->with('status', 'Товар обновлён.');
    }

    public function destroy(Product $product)
    {
        abort_unless($product->seller_id === null, 404);
        foreach ($product->images as $image) Storage::disk('public')->delete($image->path);
        $product->delete();

        return back()->with('status', 'Товар удалён.');
    }

    public function approve(Product $product)
    {
        $product->update(['status' => 'active', 'rejection_reason' => null]);
        $product->seller?->notify(new ProductReviewed($product, true));

        return back()->with('status', "Товар «{$product->name}» опубликован.");
    }

    public function reject(Request $request, Product $product)
    {
        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $product->update([
            'status' => 'rejected',
            'rejection_reason' => trim($data['rejection_reason'] ?? '') ?: 'Товар не прошёл модерацию.',
        ]);
        $product->seller?->notify(new ProductReviewed($product, false));

        return back()->with('status', "Товар «{$product->name}» отклонён.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['nullable','string','max:120'],
            'barcode' => ['nullable','string','max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'compare_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['nullable', 'in:new,used'],
            'description' => ['nullable', 'string', 'max:5000'],
            'weight'=>['nullable','numeric','min:0'],'length'=>['nullable','numeric','min:0'],'width'=>['nullable','numeric','min:0'],'height'=>['nullable','numeric','min:0'],'low_stock_threshold'=>['nullable','integer','min:0'],'seo_title'=>['nullable','string','max:255'],'seo_description'=>['nullable','string','max:1000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:8192'],
            'attribute_values' => ['nullable', 'array'],
            'attribute_values.*' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /** @param array<int|string, string|null> $values keyed by product_attribute_id */
    private function syncAttributeValues(Product $product, array $values): void
    {
        $validAttributeIds = $product->category?->attributes()->pluck('id')->all() ?? [];

        foreach ($values as $attributeId => $value) {
            if (! in_array((int) $attributeId, $validAttributeIds, true)) {
                continue;
            }

            $value = trim((string) $value);
            if ($value === '') {
                ProductAttributeValue::where('product_id', $product->id)->where('product_attribute_id', $attributeId)->delete();
                continue;
            }

            ProductAttributeValue::updateOrCreate(
                ['product_id' => $product->id, 'product_attribute_id' => $attributeId],
                ['value' => $value]
            );
        }
    }
}
