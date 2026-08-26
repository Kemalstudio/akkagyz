<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = $request->user()->products()->with('category')->latest()->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('seller.products.form', ['categories' => $categories, 'product' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_vip'] = $request->boolean('is_vip') && $request->user()->is_vip;

        $product = $request->user()->products()->create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'status' => 'pending',
        ]);

        return redirect()->route('seller.products.index')->with('status', "Товар «{$product->name}» отправлен на модерацию.");
    }

    public function edit(Request $request, Product $product)
    {
        abort_unless($product->seller_id === $request->user()->id, 403);
        $categories = Category::orderBy('sort_order')->get();

        return view('seller.products.form', compact('categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->seller_id === $request->user()->id, 403);

        $data = $this->validated($request);
        $data['is_vip'] = $request->boolean('is_vip') && $request->user()->is_vip;
        $product->update($data);

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
            'brand' => ['nullable', 'string', 'max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'compare_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
