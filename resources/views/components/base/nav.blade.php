@props([
    'active' => null,
    'ulClass' => 'nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold flex-nowrap',
    'liClass' => 'nav-item mt-2',
    'aClass' => 'nav-link text-active-primary ms-0 me-10 py-5',
    'activeClass' => 'active'
])

<ul {{ $attributes->merge(['class' => $ulClass]) }}>
    {{ $slot }}
</ul>