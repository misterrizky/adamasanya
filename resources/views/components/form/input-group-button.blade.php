@props([
    'position' => 'append',
    'click' => null,
])
<button type="button" wire:click="{{$click}}" {!! $attributes->merge(['class' => 'btn btn-primary']) !!}>{!! $slot !!}</button>