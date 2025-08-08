@props([
    'name',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'placeholder' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'size' => null,
    'modifier' => null,
    'floating' => false,
])

@php
    $inputClass = 'form-control';
    $sizeClass = $size ? 'form-control-'.$size : '';
    $inputId = $name;
@endphp

@if($floating)
    <div class="form-floating">
@endif

<input
    type="{{ $type }}"
    id="{{ $inputId }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    wire:model{{ $modifier ? ".{$modifier}" : '' }}="{{ $name }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($readonly) readonly @endif
    @if($disabled) disabled @endif
   {{ $attributes->merge(['class' => "$inputClass $sizeClass"]) }}
/>

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
    {!! $help !!}
@endif