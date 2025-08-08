@props([
    'tag' => 'button', // Default to 'button'
    'href' => null,
    'submit' => false,
    'buttonStyle' => 'default', // New styles: 'btn-flex', 'symbol'
    'buttonColor' => 'primary',
    'buttonEffect' => null,
    'textColor' => 'gray-700', // Assuming textColor might be used for styling text color dynamically
    'icon' => null,
    'iconLibrary' => '',
    'iconColor' => 'white',
    'indicator' => null,
    'indicatorProgress' => null,
    'label' => 'Tombol',
    'isSocial' => false,
    'flexContent' => null,
    'flexDescription' => null,
    'src_light' => null, // New property for light theme image source
    'src_dark' => null, // New property for dark theme image source
    // Adding classes for brand button types
    'brandButtonType' => null, // 'btn-label', 'icon-only'
])

@php
// Tag and href handling
$tagOpen = $tag === 'a' ? "<a" . ($href ? " wire:navigate href=\"{$href}\"" : '') : "<button type=\"" . ($submit ? 'submit' : 'button') . "\"";
$tagClose = $tag === 'a' ? 'a' : 'button';

// Button class handling
$baseClass = 'btn';
$colorClass = "btn-{$buttonColor}";
$effectClass = $buttonEffect ? "hover-{$buttonEffect}" : '';
$iconClass = $icon ? "{$iconLibrary} {$icon}" : '';
$socialClass = $isSocial ? "btn-icon-{$buttonColor}" : '';
$additionalClasses = $attributes->get('class');

// Special style handling
$styleClass = match($buttonStyle) {
    'light', 'dark', 'outline', 'bg', 'active', 'flush', 'flex' => "btn-{$buttonStyle}-{$buttonColor}",
    default => $colorClass,
};

$classList = implode(' ', array_filter([$baseClass, $styleClass, $effectClass, $socialClass, $additionalClasses]));

// For flex button specific content handling
if ($buttonStyle === 'flex' && $flexContent && $flexDescription) {
    $content = "<span class=\"d-flex flex-column align-items-start ms-2\"><span class=\"fs-3 fw-bold\">{$flexContent}</span><span class=\"fs-7\">{$flexDescription}</span></span>";
} else {
    $content = $label;
}
@endphp

{!! $tagOpen !!} {{!$submit ? 'wire:click='.$href : '' }} {{ $attributes->merge(['class' => $classList])}} wire:offline.attr="disabled">
    @if($src_light)
        <img alt="Logo" src="{{ asset($src_light) }}" class="{{ $src_dark ? 'theme-light-show' : '' }} h-15px me-3" />
    @endif
    @if($src_dark)
        <img alt="Logo" src="{{ asset($src_dark) }}" class="theme-dark-show h-15px me-3" />
    @endif
    @if($indicator)
        {{-- <div wire:offline>
            This device is currently offline.
        </div> --}}
        <span class="indicator-label" wire:loading.remove>
            {{ $label }}
            @if($icon && !$isSocial)
                <i class="{{ $iconClass }}" style="color: {{ $iconColor }}"></i>
            @endif
        </span>
        <span class="indicator-progress" wire:loading>
            {!! $indicator !!}
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    @else
        @if($brandButtonType !== 'icon-only')
            {{ $label }}
        @else
            {{ $slot }}
        @endif
    @endif
</{{ $tagClose }}>
