<?php

namespace App\Http\Controllers\Api\Traits;

use App\Models\Image;
use App\Models\Video;
use Illuminate\Support\Facades\File;


trait FileUploadTrait
{
    public function uploadFile($file, $folder_name)
    {
        try {

            $file_name =  time() . '_' . $file->getClientOriginalName();
            $file_full_path = $folder_name . $file_name;
            $file->move($folder_name, $file_name);
            $extension = $file->getClientOriginalExtension();

            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {

                $image = new Image();
                $image->name = $file_name;
                $image->url = $file_full_path;
                return $image;

            } elseif (in_array(strtolower($extension), ['mp4', 'avi', 'mov', 'wmv'])) {

                $video = new Video();
                $video->name = $file_name;
                $video->url = $file_full_path;
                return $video;
            }
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function deleteFile($file)
    {
        if (File::exists($file)) {
            File::delete($file);
            return true;
        }

        return false;
    }
}
