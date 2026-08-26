<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::topLevel()->with('children')->orderBy('sort_order')->get();

        $categorySections = $categories
            ->map(fn (Category $category) => [
                'category' => $category,
                'products' => Product::active()->own()->with('images')
                    ->whereIn('category_id', $category->idsWithChildren())
                    ->orderByDesc('sales_count')
                    ->orderByDesc('created_at')
                    ->limit(4)
                    ->get(),
            ])
            ->filter(fn (array $section) => $section['products']->isNotEmpty())
            ->values();

        return view('storefront.home', [
            'banners' => Banner::active()->orderBy('sort_order')->get(),
            'categories' => $categories,
            'categorySections' => $categorySections,
            'popular' => Product::active()->own()->with('images')->orderByDesc('sales_count')->limit(4)->get(),
            'newest' => Product::active()->own()->with('images')->orderByDesc('created_at')->limit(4)->get(),
        ]);
    }
}
