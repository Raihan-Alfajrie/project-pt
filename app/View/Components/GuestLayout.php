<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public ?string $title;
    public bool $standalone;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $title = null, bool $standalone = false)
    {
        $this->title = $title;
        $this->standalone = $standalone;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
