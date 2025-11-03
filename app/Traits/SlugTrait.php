<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait SlugTrait
{

    public static function createSlug($title, $id = 0, $model = null)
    {

        $slug = Str::words($title, 10);
        $slug = Str::slug($slug);
        $allSlugs = self::getRelatedSlugs($slug, $id, $model);

        if (!$allSlugs->contains('slug', $slug)) {
            return $slug;
        }

        $i = 1;

        do {
            $newSlug = $slug . '-' . $i;
            if (!$allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
            $i++;
        } while (true);

    }

    public static function getRelatedSlugs($slug, $id = 0, $model = null)
    {

        $modelInstance = $model ?? static::class;  // Use the model if provided or the calling model by default
        return $modelInstance::select('slug')->where('slug', 'like', $slug . '%')
            ->where('id', '<>', $id)
            ->get();

    }

    public static function checkSlugExist($model)
    {

        $allSlugs = static::getRelatedSlugs($model->slug, $model->id, get_class($model));
        return $allSlugs->contains('slug', $model->slug);

    }

}
