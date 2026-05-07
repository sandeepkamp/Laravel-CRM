<?php

use App\Models\UserRoleModel;
use App\Models\Department;

if (!function_exists('assignDepartmentToRole')) {
    
    function assignDepartmentToRole(string $roleName, string $departmentName): bool
    {
        $role = UserRoleModel::where('name', $roleName)->first();
        $department = Department::firstOrCreate(['name' => $departmentName]);

        if (!$role) {
            return false;
        }

        $role->department_id = $department->id;
        return $role->save();
    }
}

if (!function_exists('getRolesByDepartment')) {
    function getRolesByDepartment(string $departmentName)
    {
        return UserRoleModel::whereHas('department', function ($q) use ($departmentName) {
            $q->where('name', $departmentName);
        })->with('department')->get();
    }
}