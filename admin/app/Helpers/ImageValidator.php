<?php

namespace App\Helper;
/**
 * Created by PhpStorm.
 * User: minaret
 * Date: 08/04/2023
 * Time: 3:57 PM
 */
class ImageValidator
{
    static function validator($file, $id, $slug = null)
    {
        $extension = $file->getClientOriginalExtension();
        $fileSupport = ['jpg', 'jpeg', 'png', 'PNG', 'JPEG', 'JPG', 'gif', 'GIF'];
        if(in_array($extension, $fileSupport))
        {
            if ($slug) {
                $code = $slug;
            } else {
                $code = rand(11111,99999);
            }
            return $filename = $id.$code.'.'.$extension;
        }
        else{
            return 'Image Type Not Supported';
        }
    }

    public static function upload_picture($picture, $folder=null, $id=null)
    {
        $file_name = $id;
        if ($picture) {
            $ext = $picture->getClientOriginalExtension();
            $picture->move("images/$folder/", $file_name . "." . $ext);
            $local_url = $file_name . "." . $ext;

            $s3_url = "images/$folder/".$local_url;

            return asset('/').$s3_url;
        }
        return "";
    }
}