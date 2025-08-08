<a href="{{ $logoUrl ?? '/' }}" wire:navigate class="d-lg-none">
    @if($logo)
        <img src="{{ $logo }}" 
             alt="{{ $logoAlt ?? 'Logo' }}" 
             @if($logoWidth) width="{{ $logoWidth }}" @endif
             @if($logoHeight) height="{{ $logoHeight }}" @endif
             class="h-{{ $logoHeight ?? 20 }}px" />
    @else
        <span class="fs-2 fw-bold text-gray-800">{{ $logoAlt ?? config('app.name') }}</span>
    @endif
</a>