<x-layout :title="$activeCategory->name ?? 'Каталог'">
    @include('storefront.partials.catalog-listing', ['routeName' => 'catalog', 'homeRouteName' => 'home'])
</x-layout>
