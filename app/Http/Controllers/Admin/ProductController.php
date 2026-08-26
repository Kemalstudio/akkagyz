<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Notifications\ProductReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'seller', 'images']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->boolean('vip')) {
            $query->where('is_vip', true);
        }

        if ($request->boolean('out_of_stock')) {
            $query->where('stock', '<=', 0);
        }

        if ($request->filled('search')) {
            $term = '%'.$request->string('search').'%';
            $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('brand', 'like', $term));
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.form', ['categories' => $categories, 'product' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $product = Product::create([
            ...$data,
            'seller_id' => null,
            'slug' => Str::slug($data['name']).'-'.Str::random(6),
            'status' => 'active',
        ]);
        $this->storeImages($request, $product);

        return redirect()->route('admin.products')->with('status', "Товар «{$product->name}» добавлен и опубликован.");
    }

    public function edit(Product $product)
    {
        abort_unless($product->seller_id === null, 404);
        $product->load('images');
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.products.form', compact('categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->seller_id === null, 404);

        $product->update($this->validated($request));
        if ($request->hasFile('images')) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            $this->storeImages($request, $product);
        }

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
        $product->update(['status' => 'active']);
        $product->seller?->notify(new ProductReviewed($product, true));

        return back()->with('status', "Товар «{$product->name}» опубликован.");
    }

    public function reject(Product $product)
    {
        $product->update(['status' => 'rejected']);
        $product->seller?->notify(new ProductReviewed($product, false));

        return back()->with('status', "Товар «{$product->name}» отклонён.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:120'],
            'sku' => ['nullable','string','max:120'],
            'barcode' => ['nullable','string','max:120'],
            'price' => ['required', 'integer', 'min:0'],
            'compare_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'weight'=>['nullable','numeric','min:0'],'length'=>['nullable','numeric','min:0'],'width'=>['nullable','numeric','min:0'],'height'=>['nullable','numeric','min:0'],'low_stock_threshold'=>['nullable','integer','min:0'],'seo_title'=>['nullable','string','max:255'],'seo_description'=>['nullable','string','max:1000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:8192'],
        ]);
    }

    private function storeImages(Request $request, Product $product): void
    {
        foreach ($request->file('images', []) as $index => $image) {
            $product->images()->create(['path' => $image->store('products', 'public'), 'sort_order' => $index]);
        }
    }
}
