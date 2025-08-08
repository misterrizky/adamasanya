<?php

namespace App\View\Components;

use Closure;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class HeaderMenu extends Component
{
    public $type;
    public $active;
    public $heading;
    public $title;
    public $icon;
    public $url;
    public $items;
    public $collapsible;
    public $collapsed;
    public $showMoreText;
    public $showLessText;
    public $visibleItems;
    
    public function __construct(
        $type = 'single',
        $active = false,
        $heading = null,
        $title = null,
        $icon = null,
        $url = '#',
        $items = [],
        $collapsible = false,
        $collapsed = true,
        $showMoreText = 'Tampilkan Lebih Banyak',
        $showLessText = 'Tampilkan Lebih Sedikit',
        $visibleItems = 5
    ) {
        $this->type = $type;
        $this->active = $active;
        $this->heading = $heading;
        $this->title = $title;
        $this->icon = $icon;
        $this->url = $url;
        $this->items = $items;
        $this->collapsible = $collapsible;
        $this->collapsed = $collapsed;
        $this->showMoreText = $showMoreText;
        $this->showLessText = $showLessText;
        $this->visibleItems = $visibleItems;
    }
    
    public function render()
    {
        return view('components.partials.header.menu');
    }

    public function menuId()
    {
        return 'kt_app_header_menu_' . Str::slug($this->title);
    }

    /**
     * Check if item should be visible (not hidden in collapsible)
     */
    public function shouldBeVisible($index)
    {
        if (!$this->collapsible) {
            return true;
        }

        return $index < $this->visibleItems;
    }

    /**
     * Check if item is in collapsible section
     */
    public function isInCollapsible($index)
    {
        return $this->collapsible && $index >= $this->visibleItems;
    }
}
