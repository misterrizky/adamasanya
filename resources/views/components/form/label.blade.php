@props([
    'name' => null,
    'label' => null,
    'required' => false,
    'for' => null,
])

<label 
    for="{{ $for ?? $name }}" 
    {{ $attributes->merge(['class' => 'form-label']) }}
>
    {{ $label ?? $slot }}
    @if($required) <span class="text-danger">*</span> @endif
</label>