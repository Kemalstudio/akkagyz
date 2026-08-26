<x-marketplace-layout :title="$product->name">
    @include('storefront.partials.product-detail', ['routeName' => 'marketplace.catalog', 'homeRouteName' => 'marketplace.home'])
</x-marketplace-layout>
