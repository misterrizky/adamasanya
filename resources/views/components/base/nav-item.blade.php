@props([
    'active' => false,
    'href' => '#',
    'liClass' => 'nav-item mt-2',
    'aClass' => 'nav-link text-active-primary ms-0 me-10 py-5',
    'activeClass' => 'active'
])

<li class="{{ $liClass }}">
    <a href="{{ $href }}" 
       class="{{ $aClass }} {{ $active ? $activeClass : '' }}" 
       {{ $attributes }}>
        {{ $slot }}
    </a>
</li>