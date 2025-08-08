@props([
    'name',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'rows' => 3,
    'size' => null,
    'floating' => false,
    'modifier' => null,
    'help' => null,
])

@php
    $inputClass = 'form-control';
    $sizeClass = $size ? 'form-control-'.$size : '';
    $inputId = $name . '-' . uniqid();
@endphp

@if($floating)
    <div class="form-floating">
@endif

<textarea
    id="{{ $inputId }}"
    name="{{ $name }}"
    rows="{{ $rows }}"
    wire:model{{ $modifier ? ".{$modifier}" : '' }}="{{ $name }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($readonly) readonly @endif
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => "$inputClass $sizeClass"]) }}
>{{ old($name, $value) }}</textarea>

@if($floating && $slot->isNotEmpty())
    <label for="{{ $inputId }}">
        {{ $slot }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
@endif
@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
@if($floating)
    </div>
@endif
@if($help)
    {{ $help }}
@endif