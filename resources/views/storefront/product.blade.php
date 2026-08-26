<x-layout :title="$product->name">
    @include('storefront.partials.product-detail', ['routeName' => 'catalog', 'homeRouteName' => 'home'])
</x-layout>
