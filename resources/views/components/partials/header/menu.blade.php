@if($type === 'heading')
    <!-- Menu Heading -->
    <div class="menu-item pt-5">
        <div class="menu-content">
            <span class="menu-heading fw-bold text-uppercase fs-7">{{ $heading }}</span>
        </div>
    </div>
@elseif($type === 'submenu')
    <!-- Submenu -->
    <div data-kt-menu-trigger="click" class="menu-item {{ $active ? 'here show' : '' }} menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2" data-kt-menu-placement="bottom-start">
        <!-- Menu link -->
        <span class="menu-link">
            @if($icon)
                <span class="menu-icon">
                    <i class="ki-outline {{ $icon }} fs-2"></i>
                </span>
            @endif
            <span class="menu-title">{{ $title }}</span>
        </span>
        
        <!-- Menu sub -->
        <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
            @foreach($items as $index => $item)
                @if($shouldBeVisible($index))
                    <div class="menu-item">
                        <a class="menu-link {{ $item['active'] ?? false ? 'active' : '' }}" wire:navigate href="{{ $item['url'] ?? '#' }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">{{ $item['title'] }}</span>
                        </a>
                    </div>
                @endif
            @endforeach
            
            @if($collapsible && count($items) > $visibleItems)
                <div class="menu-inner flex-column collapse {{ !$collapsed ? 'show' : '' }}" id="{{ $menuId() }}_collapse">
                    @foreach($items as $index => $item)
                        @if($isInCollapsible($index))
                            <div class="menu-item">
                                <a class="menu-link {{ $item['active'] ?? false ? 'active' : '' }}" wire:navigate href="{{ $item['url'] ?? '#' }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">{{ $item['title'] }}</span>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <div class="menu-item">
                    <div class="menu-content">
                        <a class="btn btn-flex btn-color-primary d-flex flex-stack fs-base p-0 ms-2 mb-2 toggle collapsible {{ $collapsed ? 'collapsed' : '' }}" 
                           data-bs-toggle="collapse" 
                           href="#{{ $menuId() }}_collapse" 
                           data-kt-toggle-text="{{ $showLessText }}"
                           aria-expanded="{{ !$collapsed ? 'true' : 'false' }}">
                            <span data-kt-toggle-text-target="true">
                                {{ $showMoreText }}
                            </span>
                            <i class="ki-outline ki-minus-square toggle-on fs-2 me-0"></i>
                            <i class="ki-outline ki-plus-square toggle-off fs-2 me-0"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@else
    <!-- Single Menu Item -->
    <div class="menu-item">
        <a class="menu-link {{ $active ? 'active' : '' }}" href="{{ $url }}" wire:navigate>
            @if($icon)
                <span class="menu-icon">
                    <i class="ki-outline {{ $icon }} fs-2"></i>
                </span>
            @endif
            <span class="menu-title">{{ $title }}</span>
        </a>
    </div>
@endif