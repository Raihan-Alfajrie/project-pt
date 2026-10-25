<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public ?string $title;
    public ?string $pageTitle;
    public ?string $pageDesc;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $title = null, ?string $pageTitle = null, ?string $pageDesc = null)
    {
        $this->title = $title;
        $this->pageTitle = $pageTitle;
        $this->pageDesc = $pageDesc;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
