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

if (!function_exists('getDepartmentIdByRoleId')) {
    function getDepartmentIdByRoleId($roleId)
    {
        return UserRoleModel::where('id', $roleId)->value('department_id');
    }
}

if (!function_exists('getDepartmentNameByRoleId')) {
    function getDepartmentNameByRoleId($roleId)
    {
        // $role = UserRoleModel::with('department')->find($roleId);
        // return ($role && $role->department) ? $role->department->name : null;

        $departmentId = UserRoleModel::where('id', $roleId)->value('department_id');
        return $departmentId ? Department::where('id', $departmentId)->value('name') : null;
    }
}

if (!function_exists('getRolesByDepartmentId')) {
    function getRolesByDepartmentId(int $departmentId)
    {
        return UserRoleModel::where('department_id', $departmentId)->get(['id', 'name']); 
    }
}