<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DataTableCard extends Component
{
    public $title;
    public $tableId;
    public $tableClass;
    public $search;
    public $button;
    public $route;
    public $buttonName;

    public function __construct($title, $tableId, $tableClass = '', $search = false, $button = false, $route = null, $buttonName = null)
    {
        $this->title = $title;
        $this->tableId = $tableId;
        $this->tableClass = $tableClass;
        $this->search = filter_var($search, FILTER_VALIDATE_BOOLEAN);
        $this->button = filter_var($button, FILTER_VALIDATE_BOOLEAN);
        $this->route = $route;
        $this->buttonName = $buttonName;
    }

    public function render()
    {
        return view('components.data-table-card');
    }
}
