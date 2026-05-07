<?php

// ---------------single file upload----------------------
if (!function_exists('uploadFile')) {
    function uploadFile($file, string $path = 'uploads')
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $uploadPath = public_path($path);
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $originalName = strtolower(str_replace(' ', '-', $file->getClientOriginalName()));
        $extension = $file->getClientOriginalExtension();
        $file_type = $file->getClientMimeType();
        $file_size = $file->getSize();

        $modifiedName = uniqid() . '_' . time() . '.' . $extension;
        $file->move($uploadPath, $modifiedName);

        return [
            'path' => $path . '/' . $modifiedName,
            'original_name' => $originalName,
            'modified_name' => $modifiedName,
            'file_type' => $file_type,
            'file_size' => $file_size,
        ];
    }
}

// ----------------------multiple file upload-------------------------
if (!function_exists('uploadMultipleFiles')) {
    function uploadMultipleFiles(array $files, string $path)
    {
        $uploaded = [];

        foreach ($files as $file) {
            $result = uploadFile($file, $path);
            if ($result) {
                $uploaded[] = $result;
            }
        }

        return $uploaded;
    }

}

// ---------------delete single file-------------------
if (!function_exists('deleteFile')) {
    function deleteFile(?string $relativePath = '')
    {
        if (!$relativePath) {
            return false;
        }

        $fullPath = public_path($relativePath);

        if (isset($fullPath) && file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}

// --------------------delete multiple files-----------------
if (!function_exists('deleteMultipleFiles')) {
    function deleteMultipleFiles(array $paths)
    {
        foreach ($paths as $path) {
            deleteFile(UploadPaths::TASKS . '/' . $path);
        }
    }
}
