<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RowActions extends Component
{
    public function __construct(
        public string $editRoute,
        public string $deleteRoute,
        public string $itemName = 'este registro',
        public ?string $showRoute = null,
    ) {}

    public function render()
    {
        return view('components.row-actions');
    }

    public function modalId(): string
    {
        return 'modalDelete_' . md5($this->deleteRoute);
    }
}