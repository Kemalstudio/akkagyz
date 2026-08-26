<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductAttributeFilterService
{
    /**
     * The attribute definitions for the exact category the customer is
     * browsing. Attributes are not inherited from a parent category — they
     * only apply once a specific category is selected.
     *
     * @return Collection<int, ProductAttribute>
     */
    public function attributesFor(?Category $category): Collection
    {
        if (! $category) {
            return collect();
        }

        return ProductAttribute::where('category_id', $category->id)->orderBy('sort_order')->get();
    }

    /**
     * For each attribute, collect the distinct values (with counts) available
     * among the products currently matched by $query, so the filter panel can
     * render checkboxes for exactly the values that exist in this result set.
     *
     * @param  Collection<int, ProductAttribute>  $attributes
     * @return Collection<int, array{attribute: ProductAttribute, values: Collection}>
     */
    public function facets(Builder $query, Collection $attributes): Collection
    {
        if ($attributes->isEmpty()) {
            return collect();
        }

        $productIds = (clone $query)->pluck('products.id');

        return $attributes
            ->map(function (ProductAttribute $attribute) use ($productIds) {
                $values = ProductAttributeValue::query()
                    ->where('product_attribute_id', $attribute->id)
                    ->whereIn('product_id', $productIds)
                    ->selectRaw('value, count(*) as aggregate')
                    ->groupBy('value')
                    ->orderBy('value')
                    ->get();

                return ['attribute' => $attribute, 'values' => $values];
            })
            ->filter(fn (array $facet) => $facet['values']->isNotEmpty())
            ->values();
    }

    /**
     * Apply the customer's selected checkboxes (request `attr[{id}][]`) to the
     * query. Values within one attribute are OR'd together; different
     * attributes are AND'd together.
     *
     * @param  Collection<int, ProductAttribute>  $attributes
     */
    public function applySelected(Builder $query, Request $request, Collection $attributes): void
    {
        $selected = $request->input('attr', []);
        if (! is_array($selected) || $attributes->isEmpty()) {
            return;
        }

        foreach ($attributes as $attribute) {
            $values = $selected[$attribute->id] ?? null;
            if (! is_array($values) || empty($values)) {
                continue;
            }

            $query->whereHas('attributeValues', function (Builder $q) use ($attribute, $values) {
                $q->where('product_attribute_id', $attribute->id)->whereIn('value', $values);
            });
        }
    }

    /** Whether any attribute checkbox is currently selected in the request. */
    public function hasSelected(Request $request): bool
    {
        $selected = $request->input('attr', []);

        return is_array($selected) && collect($selected)->filter()->isNotEmpty();
    }
}
