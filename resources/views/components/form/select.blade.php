@props([
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'size' => null,
    'floating' => false,
    'modifier' => null,
    'help' => null,
])

@php
    $inputClass = 'form-select';
    $sizeClass = $size ? 'form-select-'.$size : '';
    $inputId = $name . '-' . uniqid();
    $selected = old($name, $selected);
@endphp

@if($floating)
    <div class="form-floating">
@endif

<select
    id="{{ $inputId }}"
    name="{{ $name }}"
    wire:model{{ $modifier ? ".{$modifier}" : '' }}="{{ $name }}"
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($multiple) multiple @endif
    {{ $attributes->merge(['class' => "$inputClass $sizeClass"]) }}
>
    @if($placeholder)
        <option value="" @if(is_null($selected)) selected @endif>{{ $placeholder }}</option>
    @endif

    @foreach($options as $value => $text)
        <option value="{{ $value }}" @if($selected == $value) selected @endif>
            {{ $text }}
        </option>
    @endforeach
</select>

@if($floating && $slot->isNotEmpty())
    <label for="{{ $inputId }}">
        {{ $slot }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
@endif

@if($floating)
    </div>
@endif
@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
@if($help)
    {!! $help !!}
@endif