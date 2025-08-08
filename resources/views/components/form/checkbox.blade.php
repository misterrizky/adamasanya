@props([
    'id',
    'name',
    'label' => null,
    'value' => null,
    'help' => null,
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'help' => null,
    'inline' => false,
    'switch' => false,
    'modifier' => null,
])

@php
    $inputId = $name . '-' . uniqid();
    $checked = (bool) old($name, $checked);
@endphp

<div class="mb-3 {{ $inline ? 'form-check-inline' : '' }} {{ $attributes->get('class') }}">
    <div class="form-check @if($switch) form-switch @endif">
        <input
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value ?? 1 }}"
            wire:model="{{ $name }}"
            @if($checked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'form-check-input']) }}
        />
        @if($label)
            <label class="form-check-label" for="{{ $id }}">
                {{ $label }}
                @if($required) <span class="text-danger">*</span> @endif
            </label>
        @endif
        @if($help)
            {!! $help !!}
        @endif
    </div>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>