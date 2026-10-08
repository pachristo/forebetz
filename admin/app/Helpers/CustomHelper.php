<?php

namespace App\Helper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomHelper
{
    public static function upload_picture($picture, $name)
    {
        $file_name = Str::slug($name, '_');
        $file_name .= '-'.rand();
        if ($picture) {
            $ext = $picture->getClientOriginalExtension();
            $picture->move('uploads', $file_name.'.'.$ext);
            $local_url = $file_name.'.'.$ext;

            $url = url('/').'/uploads/'.$local_url;

            return [$url, $ext];
        }

        return '';
    }

    public static function upload_base64_picture($picture, $entity_type, $entity_id, $name)
    {
        $file_name = Str::slug($name, '_');
        $file_name .= '-'.rand();

        if ($picture) {
            $file = base64_decode($picture);

            $d = getimagesizefromstring($file);
            $ext = image_type_to_extension($d[2]);

            $local_file_path = $entity_type.DIRECTORY_SEPARATOR.$entity_id;
            if (! Storage::exists($local_file_path)) {
                Storage::makeDirectory($local_file_path, 0775, true, true);
            }
            $full_path = $local_file_path.DIRECTORY_SEPARATOR.$file_name.$ext;

            Storage::put($full_path, $file);

            return [$full_path, $ext];
        }

        return '';
    }

    public static function sanitizeInput($posts_data, $exempted = [], $default_filter = FILTER_SANITIZE_STRING)
    {
        if (! is_array($posts_data)) {
            $posts_data = [$posts_data];
        }
        $args = [];
        $within_db_field_limit = [];
        foreach ($posts_data as $prk => $prv) {
            if (is_array($prv)) {
                $args[$prk] = [
                    'filter' => $default_filter,
                    'flags' => FILTER_REQUIRE_ARRAY,
                ];
            } else {
                if (! in_array($prk, array_keys($args))) {
                    if (is_array($exempted) && in_array($prk, $exempted)) {
                        $args[$prk] = '';
                    } else {
                        $args[$prk] = FILTER_SANITIZE_STRING;
                    }
                }
            }
        }
        $post_request_data = filter_var_array($posts_data, $args);
        if ($within_db_field_limit) {
            foreach ($within_db_field_limit as $key => $end) {
                $val = $post_request_data[$key];
                $post_request_data[$key] = substr($val, 0, $end);
            }
        }

        return $post_request_data;
        //return array_map('utf8_encode', $post_request_data);
    }

    public static function clean($string)
    {
        $string = utf8_encode($string);
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = preg_replace('/[^a-z0-9- ]/i', '', $string);
        $string = str_replace(' ', '-', $string);

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    public static function delete_picture($picture)
    {
        File::delete(public_path().'/uploads/'.basename($picture));

        return true;
    }
}
