@props([
    'label' => 'Submit',
    'color' => 'primary',
    'outline' => false,
    'size' => null,
])

@php
    $buttonClass = 'btn';
    $buttonClass .= $outline ? ' btn-outline-' . $color : ' btn-' . $color;
    $buttonClass .= $size ? ' btn-' . $size : '';
@endphp

<button type="submit" {{ $attributes->merge(['class' => $buttonClass]) }}>
    {{ $label ?? $slot }}
</button>