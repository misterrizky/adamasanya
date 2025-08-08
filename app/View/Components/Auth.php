<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Auth extends Component
{
    public string $theme;
    public string $layout;
    public bool $darkMode;
    public ?string $direction;
    public array $layoutConfig;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $theme = 'light',
        string $layout = 'light-sidebar',
        bool $darkMode = false,
        ?string $direction = null,
        bool $pageLoadingEnabled = true,
        bool $headerFixed = true,
        bool $headerFixedMobile = true,
        bool $sidebarEnabled = true,
        bool $sidebarFixed = true,
        bool $sidebarMinimize = true,
        bool $sidebarHoverable = true,
        bool $sidebarPushHeader = true,
        bool $sidebarPushToolbar = true,
        bool $sidebarPushFooter = true,
        bool $toolbarEnabled = true,
        bool $footerFixed = false,
        bool $footerFixedMobile = false
    ) {
        $this->theme = $theme;
        $this->layout = $layout;
        $this->darkMode = $darkMode;
        $this->direction = $direction ?? (app()->isLocale('ar') ? 'rtl' : 'ltr');
        
        $this->layoutConfig = [
            'page-loading-enabled' => $pageLoadingEnabled,
            'header-fixed' => $headerFixed,
            'header-fixed-mobile' => $headerFixedMobile,
            'sidebar-enabled' => $sidebarEnabled,
            'sidebar-fixed' => $sidebarFixed,
            'sidebar-minimize' => $sidebarMinimize,
            'sidebar-hoverable' => $sidebarHoverable,
            'sidebar-push-header' => $sidebarPushHeader,
            'sidebar-push-toolbar' => $sidebarPushToolbar,
            'sidebar-push-footer' => $sidebarPushFooter,
            'toolbar-enabled' => $toolbarEnabled,
            'footer-fixed' => $footerFixed,
            'footer-fixed-mobile' => $footerFixedMobile,
        ];
    }
    public function layoutAttributes(): string
    {
        $attributes = [];
        foreach ($this->layoutConfig as $key => $value) {
            $attrName = 'data-kt-app-' . str_replace('_', '-', $key);
            $attrValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            $attributes[] = $attrName . '="' . $attrValue . '"';
        }
        
        return implode(' ', $attributes);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view("components.auth", [
            'theme' => $this->theme,
            'layout' => $this->layout,
            'darkMode' => $this->darkMode,
            'direction' => $this->direction,
            'layoutAttributes' => $this->layoutAttributes(),
            'layoutClass' => 'app-' . str_replace('_', '-', $this->layout),
        ]);
    }
}
