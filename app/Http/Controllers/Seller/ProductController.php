<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Support\Concerns\HandlesProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HandlesProductImages;

    public function index(Request $request)
    {
        $products = $request->user()->products()->with('category')->latest()->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('attributes')->orderBy('sort_order')->get();

        return view('seller.products.form', ['categories' => $categories, 'product' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_vip'] = $request->boolean('is_vip') && $request->user()->is_vip;
        $attributeValues = $data['attribute_values'] ?? [];
        unset($data['attribute_values']);

        $product = $request->user()->products()->create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'status' => 'pending',
        ]);
        $this->storeProductImages($request, $product);
        $this->syncAttributeValues($product, $attributeValues);

        return redirect()->route('seller.products.index')->with('status', "Товар «{$product->name}» отправлен на модерацию.");
    }

    public function edit(Request $request, Product $product)
    {
        abort_unless($product->seller_id === $request->user()->id, 403);
        $product->load('attributeValues', 'images');
        $categories = Category::with('attributes')->orderBy('sort_order')->get();

        return view('seller.products.form', compact('categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->seller_id === $request->user()->id, 403);

        $data = $this->validated($request);
        $data['is_vip'] = $request->boolean('is_vip') && $request->user()->is_vip;
        $attributeValues = $data['attribute_values'] ?? [];
        unset($data['attribute_values']);

        $product->update($data);
        if ($request->hasFile('images')) {
            $this->replaceProductImages($request, $product);
        }
        $this->syncAttributeValues($product, $attributeValues);

        return redirect()->route('seller.products.index')->with('status', 'Товар обновлён.');
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->seller_id === $request->user()->id, 403);
        $product->delete();

        return back()->with('status', 'Товар удалён.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:0'],
            'compare_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'in:new,used'],
            'description' => ['nullable', 'string', 'max:5000'],
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
