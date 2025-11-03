<?php

namespace App\Traits\Treatment;

trait AddLazyLoadingToImages
{

    public function addLazyLoadingToImages($treatment)
    {

        // Use a regular expression to find all <img> tags and add loading="lazy"
        $pattern = '/<img([^>]+)>/';
        $replacement = '<img$1 loading="lazy">';
        $treatment->terms_conditions = preg_replace($pattern, $replacement, $treatment->terms_conditions);

    }

}
