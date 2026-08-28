<?php

namespace App\Support;

/**
 * One filter dimension shown in the catalog sidebar. Usually backed by a
 * single ProductAttribute, but when a customer browses a branch category
 * (a section, or any category with subcategories) attributes with the same
 * name defined on different subcategories are merged into one group so the
 * panel shows a single "Тип" filter instead of one per subcategory.
 */
final class AttributeFilterGroup
{
    /** @param array<int, int> $attributeIds */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $filter_icon,
        public readonly array $attributeIds,
    ) {
    }
}
