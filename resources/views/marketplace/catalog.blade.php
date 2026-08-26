<x-marketplace-layout :title="$activeCategory->name ?? 'Каталог витрины'">
    @include('storefront.partials.catalog-listing', ['routeName' => 'marketplace.catalog', 'homeRouteName' => 'marketplace.home'])
</x-marketplace-layout>
