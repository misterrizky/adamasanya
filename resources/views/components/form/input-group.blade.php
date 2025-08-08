@props([
    'prepend' => null,
    'append' => null,
])

<div class="input-group {{ $attributes->get('class') }}">
    @if($prepend)
        <span class="input-group-text">{!! $prepend !!}</span>
    @endif

    {{ $slot }}

    @if($append)
        <span class="input-group-text">{!! $append !!}</span>
    @endif
</div>