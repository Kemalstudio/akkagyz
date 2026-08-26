@props(['categories', 'product' => null])
@php
    $categoryAttributesMap = $categories->mapWithKeys(fn ($cat) => [
        $cat->id => $cat->attributes->map(fn ($attr) => ['id' => $attr->id, 'name' => $attr->name])->values(),
    ]);
    $existingValues = $product
        ? $product->attributeValues->mapWithKeys(fn ($value) => [$value->product_attribute_id => $value->value])
        : collect();
@endphp
<section class="card editor-section" id="attribute-fields-section" style="display:none;">
    <div class="section-title">Характеристики</div>
    <div class="form-grid" id="attribute-fields-grid"></div>
</section>
<script>
(function () {
    var categoryAttributes = @json($categoryAttributesMap);
    var existingValues = @json($existingValues);
    var select = document.querySelector('select[name="category_id"]');
    var section = document.getElementById('attribute-fields-section');
    var grid = document.getElementById('attribute-fields-grid');
    if (!select || !section || !grid) return;

    function render() {
        var attributes = categoryAttributes[select.value] || [];
        grid.innerHTML = '';
        if (!attributes.length) {
            section.style.display = 'none';
            return;
        }
        section.style.display = '';
        attributes.forEach(function (attribute) {
            var field = document.createElement('div');
            field.className = 'field';
            var label = document.createElement('label');
            label.textContent = attribute.name;
            var input = document.createElement('input');
            input.className = 'input';
            input.name = 'attribute_values[' + attribute.id + ']';
            input.value = existingValues[attribute.id] || '';
            input.placeholder = 'Значение для этого товара';
            field.appendChild(label);
            field.appendChild(input);
            grid.appendChild(field);
        });
    }

    select.addEventListener('change', render);
    render();
})();
</script>
