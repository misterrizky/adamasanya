<div id="kt_app_toolbar" class="app-toolbar {{ $toolbarClass }} d-none d-xl-block">
    <div id="kt_app_toolbar_container" class="app-container {{ $containerClass }} d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            @if(!empty($breadcrumbs))
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    @foreach($breadcrumbs as $index => $item)
                        <!--begin::Item-->
                        <li class="breadcrumb-item {{ $item['active'] ?? false ? 'text-muted' : '' }}">
                            @if($item['url'] ?? false)
                                <a href="{{ $item['url'] }}" wire:navigate class="text-muted text-hover-primary">
                                    @if(isset($item['text']))
                                        {{ $item['text'] }}
                                    @endif
                                    @if(isset($item['icon']))
                                    <i class="ki-outline ki-{{ $item['icon'] }} text-gray-700 fs-6"></i>
                                    @endif
                                </a>
                            @else
                                {{ $item['text'] }}
                            @endif
                        </li>
                        <!--end::Item-->
                        
                        @if(!$loop->last)
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                        @endif
                    @endforeach
                </ul>
                <!--end::Breadcrumb-->
            @endif
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                {{ $title }}
            </h1>
            <!--end::Title-->
        </div>
        <!--end::Page title-->
        
        @if(!empty($buttons))
            <!--begin::Actions-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @foreach($buttons as $button)
                    <a href="{{ $button['url'] ?? '#' }}" {{ isset($button['url']) ? 'wire:navigate' : '' }} 
                       class="btn btn-sm fw-bold btn-{{ $button['type'] ?? 'primary' }}"
                       @foreach($button['attributes'] ?? [] as $attr => $value)
                           {{ $attr }}="{{ $value }}"
                       @endforeach>
                        {{ $button['text'] }}
                    </a>
                @endforeach
            </div>
            <!--end::Actions-->
        @endif
    </div>
</div>