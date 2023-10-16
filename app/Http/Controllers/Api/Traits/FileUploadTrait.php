<?php

namespace App\Http\Controllers\Api\Traits;

use Illuminate\Support\Facades\File;


trait FileUploadTrait
{
    public function uploadFile($request, $input = "image", $folder_name)
    {
        try {
                if ($request->hasFile($input)) {
                $file = $request->file($input);
                $file_name =  time() . '_'. $file->getClientOriginalName();
                $file_full_path = $folder_name . $file_name;
                $file->move($folder_name, $file_name);
                
                return [$file_name, $file_full_path];
                }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function deleteFile($file_name)
    {
        if (File::exists($file_name)) {
            File::delete($file_name);
            return true;
        } 

        return false;
    }
}