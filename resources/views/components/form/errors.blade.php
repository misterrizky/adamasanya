@props([
    'errors' => null,
    'bag' => 'default',
])

@php
    $errors = $errors ?? session('errors');
@endphp

@if ($errors && $errors->getBag($bag)->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger']) }}>
        <ul class="mb-0">
            @foreach ($errors->getBag($bag)->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif