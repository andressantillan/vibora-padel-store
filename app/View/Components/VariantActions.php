<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\ProductVariant;


class VariantActions extends Component
{
    public function __construct(public ProductVariant $variant) {}

    public function render()
    {
        return view('components.variant-actions');
    }
}
