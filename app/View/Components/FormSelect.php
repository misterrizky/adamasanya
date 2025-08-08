<?php

namespace App\View\Components;

use Closure;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class FormSelect extends Component
{
    public function __construct() {
        // 
    }
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
