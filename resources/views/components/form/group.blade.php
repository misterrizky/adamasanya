@props([
    'name' => null,
    'label' => null,
    'help' => null,
    'required' => false,
    'floating' => false,
])

<div {{ $attributes->merge(['class' => 'form-group']) }}>
    @if($label && !$floating)
        <x-form-label :name="$name" :label="$label" :required="$required" />
    @endif

    {{ $slot }}

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>