<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Support\AttributeFilterGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductAttributeFilterService
{
    /**
     * The filter groups relevant to the category the customer is browsing:
     *  - Browsing a branch (a category with subcategories, including a
     *    top-level section) shows the union of attributes defined anywhere
     *    in that subtree, so a section page still surfaces useful filters
     *    before a specific subcategory is picked.
     *  - Browsing a leaf category shows its own attributes, or — if it has
     *    none of its own — the nearest ancestor's, so a subcategory that was
     *    never given its own characteristics still inherits its parent's
     *    filters instead of showing none at all.
     *
     * Attributes that share a name (which happens once a branch's subtree is
     * unioned — e.g. several subcategories each defining their own "Тип")
     * are merged into a single group so the panel doesn't show duplicates.
     *
     * @return Collection<int, AttributeFilterGroup>
     */
    public function attributesFor(?Category $category): Collection
    {
        if (! $category) {
            return collect();
        }

        $attributes = $this->rawAttributesFor($category);

        return $attributes
            ->groupBy('name')
            ->map(function (Collection $group) {
                $first = $group->first();
                $ids = $group->pluck('id')->sort()->values()->all();

                return new AttributeFilterGroup(
                    id: implode('-', $ids),
                    name: $first->name,
                    filter_icon: $first->filter_icon,
                    attributeIds: $ids,
                );
            })
            ->sortBy(fn (AttributeFilterGroup $group) => min($group->attributeIds))
            ->values();
    }

    /** @return Collection<int, ProductAttribute> */
    private function rawAttributesFor(Category $category): Collection
    {
        if ($category->children()->exists()) {
            return ProductAttribute::whereIn('category_id', $category->idsWithChildren())
                ->orderBy('sort_order')
                ->get();
        }

        for ($current = $category; $current; $current = $current->parent) {
            $attributes = ProductAttribute::where('category_id', $current->id)->orderBy('sort_order')->get();
            if ($attributes->isNotEmpty()) {
                return $attributes;
            }
        }

        return collect();
    }

    /**
     * For each filter group, collect the distinct values (with counts)
     * available among the products currently matched by $query, so the
     * filter panel can render checkboxes for exactly the values that exist
     * in this result set.
     *
     * @param  Collection<int, AttributeFilterGroup>  $groups
     * @return Collection<int, array{attribute: AttributeFilterGroup, values: Collection}>
     */
    public function facets(Builder $query, Collection $groups): Collection
    {
        if ($groups->isEmpty()) {
            return collect();
        }

        $matchingProducts = (clone $query)->select('products.id');

        return $groups
            ->map(function (AttributeFilterGroup $group) use ($matchingProducts) {
                $values = ProductAttributeValue::query()
                    ->whereIn('product_attribute_id', $group->attributeIds)
                    ->whereIn('product_id', clone $matchingProducts)
                    ->selectRaw('value, count(*) as aggregate')
                    ->groupBy('value')
                    ->orderBy('value')
                    ->get();

                return ['attribute' => $group, 'values' => $values];
            })
            ->filter(fn (array $facet) => $facet['values']->isNotEmpty())
            ->values();
    }

    /**
     * Apply the customer's selected checkboxes (request `attr[{groupId}][]`)
     * to the query. Values within one group are OR'd together; different
     * groups are AND'd together.
     *
     * @param  Collection<int, AttributeFilterGroup>  $groups
     */
    public function applySelected(Builder $query, Request $request, Collection $groups): void
    {
        $selected = $request->input('attr', []);
        if (! is_array($selected) || $groups->isEmpty()) {
            return;
        }

        foreach ($groups as $group) {
            $values = $selected[$group->id] ?? null;
            if (! is_array($values) || empty($values)) {
                continue;
            }

            $query->whereHas('attributeValues', function (Builder $q) use ($group, $values) {
                $q->whereIn('product_attribute_id', $group->attributeIds)->whereIn('value', $values);
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
