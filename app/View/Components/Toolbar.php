<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toolbar extends Component
{
    public string $title;
    public array $breadcrumbs;
    public array $buttons;
    public string $containerClass;
    public string $toolbarClass;
    
    public function __construct(
        string $title,
        array $breadcrumbs = [],
        array $buttons = [],
        string $containerClass = 'container-fluid',
        string $toolbarClass = 'py-3 py-lg-6'
    ) {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;
        $this->buttons = $buttons;
        $this->containerClass = $containerClass;
        $this->toolbarClass = $toolbarClass;
    }
    public function render(): View|Closure|string
    {
        return view('components.base.toolbar');
    }
}
