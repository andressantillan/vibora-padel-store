<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShowActions extends Component
{
    public function __construct(
        public string $editRoute,
        public string $backRoute,
    ) {}

    public function render()
    {
        return view('components.show-actions');
    }
}

