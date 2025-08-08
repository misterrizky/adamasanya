@props([
    'name',
    'label' => null,
    'value' => null,
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'help' => null,
    'inline' => false,
    'modifier' => null,
])

@php
    $inputId = $name . '-' . uniqid() . '-' . Str::slug($value);
    $checked = old($name, $checked) == $value;
@endphp

<div class="mb-3 {{ $inline ? 'form-check-inline' : '' }} {{ $attributes->get('class') }}">
    <div class="form-check">
        <input
            type="radio"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $value }}"
            wire:model{{ $modifier ? ".{$modifier}" : '' }}="{{ $name }}"
            @if($checked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'form-check-input']) }}
        />

        @if($label)
            <label class="form-check-label" for="{{ $inputId }}">
                {{ $label }}
                @if($required) <span class="text-danger">*</span> @endif
            </label>
        @endif
    </div>

    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>