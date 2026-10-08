<?php

namespace App\Services;

use App\Models\Meta;

class CategoryService
{
    public static function newCategory($name): bool
    {
        $categories = Meta::where('key', 'categories')->first()?->value;
        if (!$categories)
        {
            $category = [$name];
            Meta::create([
                'key' => 'categories',
                'value' => json_encode($category)
            ]);
            return true;
        }

        $category = json_decode($categories);
        if (!in_array($name, $category))
        {
            $category[] = $name;
            Meta::where('key', 'categories')->update(['value'=>json_encode($category)]);
        }
        return true;
    }

    public static function getCategories(): array|object
    {
        $cats = Meta::where('key', 'categories')->first()?->value;
        if ($cats) return json_decode($cats);
        return [];
    }
}
