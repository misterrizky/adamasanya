@props([
    'action' => null,
    'hasFiles' => null,
])
<form
    class="{{ $attributes->get('class') }}"
    wire:submit.prevent="{{ $action }}"
    {!! $hasFiles ? 'enctype="multipart/form-data"' : '' !!}
    {{ $attributes->merge(['class' => 'needs-validation']) }}
    novalidate
    >
    {!! $slot !!}
</form>