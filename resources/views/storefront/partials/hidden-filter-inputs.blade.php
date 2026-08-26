@foreach($data as $key => $value)
    @if(is_array($value))
        @include('storefront.partials.hidden-filter-inputs', ['data' => $value, 'prefix' => $prefix ? $prefix.'['.$key.']' : $key])
    @else
        <input type="hidden" name="{{ $prefix ? $prefix.'['.$key.']' : $key }}" value="{{ $value }}">
    @endif
@endforeach
