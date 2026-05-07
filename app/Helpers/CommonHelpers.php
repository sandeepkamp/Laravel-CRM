<?php

if (!function_exists('renderAssignBadge')) {
    function renderAssignBadge($assigns) {
        if (!$assigns instanceof \Illuminate\Support\Collection || $assigns->isEmpty()) {
            return '';
        }

        $firstAssign = $assigns->first();
        $otherAssigns = $assigns->slice(1);

        $tooltipIcon = '';
        if ($otherAssigns->count() > 0) {
            $tooltipData = htmlspecialchars(
                $otherAssigns->map(fn($a) => e($a->first_name . ' ' . $a->last_name))->implode('<br>'),
                ENT_QUOTES,
                'UTF-8'
            );

            $tooltipIcon = ' <i class="mdi mdi-dots-horizontal-circle text-primary" style="cursor:pointer;" 
                                data-bs-toggle="tooltip" 
                                data-bs-html="true" 
                                title="'.$tooltipData.'"></i>';
        }

        $padding = ($otherAssigns->count() > 0) ? 'style="padding: 0px 6px;"' : '';

        return '<span class="badge rounded-pill bg-label-primary role-' . $firstAssign->role . '" ' . $padding . '>'
            . e($firstAssign->first_name . ' ' . $firstAssign->last_name)
            . $tooltipIcon .
            '</span>';
    }
}

if (!function_exists('getProjectStatusList')) {
    function getProjectStatusList() {
        return [
            0 => ['text' => 'Not Started', 'color' => 'bg-label-secondary'],
            1 => ['text' => 'Initiation Stage', 'color' => 'bg-label-primary'],
            2 => ['text' => 'Planning Stage', 'color' => 'bg-label-info'],
            3 => ['text' => 'Execution Stage', 'color' => 'bg-label-warning'],
            4 => ['text' => 'Monitoring & Controlling Stage', 'color' => 'bg-label-dark'],
            5 => ['text' => 'Testing Phase', 'color' => 'bg-label-danger'],
            6 => ['text' => 'Closure Stage', 'color' => 'bg-label-success'],
            7 => ['text' => 'Completed', 'color' => 'bg-label-success'],
            8 => ['text' => 'Handed Over to Client', 'color' => 'bg-label-success'],
        ];
    }
}

if (!function_exists('getUserStatus')) {
    function getUserStatus() {
        return [
            1 => ['text' => 'Active', 'class' => 'badge rounded-pill bg-label-success'],
            2 => ['text' => 'Inactive', 'class' => 'badge rounded-pill bg-label-warning'],
            3 => ['text' => 'Blocked', 'class' => 'badge rounded-pill bg-label-danger'],
        ];
    }
}

if (!function_exists('roleLabels')) {
    function roleLabels() {
        return [
            1 => ['class' => 'badge rounded-pill bg-label-danger'],
            2 => ['class' => 'badge rounded-pill bg-label-primary'],
            3 => ['class' => 'badge rounded-pill bg-label-warning'],
            4 => ['class' => 'badge rounded-pill bg-label-dark'],
            5 => ['class' => 'badge rounded-pill bg-label-secondary'],
            6 => ['class' => 'badge rounded-pill bg-label-light'],
            7 => ['class' => 'badge rounded-pill bg-label-success'],
            8 => ['class' => 'badge rounded-pill bg-label-info'],
        ];
    }
}

if (!function_exists('generateRouteData')) {
    function generateRouteData($value)
    {
        $action = str_contains($value, '-') ? lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $value)))) : $value;
        $name = str_replace('-', '.', $value);
        return [$action, $name];
    }
}

if (!function_exists('method')) {
    function method($value)
    {
        $action = str_contains($value, '-') ? lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $value)))) : $value;
        return $action;
    }
}