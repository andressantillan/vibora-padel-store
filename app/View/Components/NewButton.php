<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NewButton extends Component
{
    public function __construct(
        public string $route,
        public string $label = 'Nuevo',
    ) {}

    public function render()
    {
        return view('components.new-button');
    }
}
