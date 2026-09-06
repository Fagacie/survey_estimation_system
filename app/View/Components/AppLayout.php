<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public $hideSidebar;
    public $containerClass;

    public function __construct($hideSidebar = false, $containerClass = null)
    {
        $this->hideSidebar = filter_var($hideSidebar, FILTER_VALIDATE_BOOLEAN);
        $this->containerClass = $containerClass;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
