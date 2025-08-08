<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ToolbarMobile extends Component
{
    /**
     * Create a new component instance.
     */
    public array $breadcrumbs;
    public array $buttons;
    public function __construct(array $breadcrumbs = [], array $buttons = [])
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->buttons = $buttons;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.base.toolbar-mobile');
    }
}
