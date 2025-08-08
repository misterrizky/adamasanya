@props([
    'name',
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => null,
    'disabled' => false,
    'modifier' => null,
    'help' => null,
])

@php
    $inputId = $name . '-' . uniqid();
@endphp

<input
    type="range"
    id="{{ $inputId }}"
    name="{{ $name }}"
    min="{{ $min }}"
    max="{{ $max }}"
    step="{{ $step }}"
    wire:model{{ $modifier ? ".{$modifier}" : '' }}="{{ $name }}"
    value="{{ old($name, $value) }}"
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => 'form-range']) }}
/>
@if($help)
    {{ $help }}
@endif