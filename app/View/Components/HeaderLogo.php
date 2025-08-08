<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderLogo extends Component
{
    public function __construct()
    {
        // 
    }
    public function render(): View|Closure|string
    {
        return view('components.partials.header.logo', [
            'logo' => $this->logo ?? null,
            'logoWidth' => $this->logoWidth ?? 'auto',
            'logoHeight' => $this->logoHeight ?? 'auto',
            'logoUrl' => $this->logoUrl ?? '/',
            'logoAlt' => $this->logoAlt ?? config('app.name'),
        ]);
    }
}
