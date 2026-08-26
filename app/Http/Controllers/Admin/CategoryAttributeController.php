<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryAttributeController extends Controller
{
    public function index(Category $category)
    {
        $attributes = $category->attributes()->withCount('values')->get();

        return view('admin.categories.attributes', compact('category', 'attributes'));
    }

    public function store(Request $request, Category $category)
    {
        $data = $this->validated($request, $category);
        $data['sort_order'] = $data['sort_order'] ?? ($category->attributes()->max('sort_order') + 1);
        $category->attributes()->create($data);

        return back()->with('status', 'Характеристика добавлена.');
    }

    public function update(Request $request, Category $category, ProductAttribute $attribute)
    {
        abort_unless($attribute->category_id === $category->id, 404);
        $attribute->update($this->validated($request, $category, $attribute));

        return back()->with('status', 'Характеристика обновлена.');
    }

    public function destroy(Category $category, ProductAttribute $attribute)
    {
        abort_unless($attribute->category_id === $category->id, 404);
        $attribute->delete();

        return back()->with('status', 'Характеристика удалена вместе со значениями у товаров.');
    }

    private function validated(Request $request, Category $category, ?ProductAttribute $attribute = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('product_attributes', 'name')->where('category_id', $category->id)->ignore($attribute?->id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
