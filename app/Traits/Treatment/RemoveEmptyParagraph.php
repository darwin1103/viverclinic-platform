<?php

namespace App\Traits\Treatment;

trait RemoveEmptyParagraph
{

    public function removeEmptyParagraph($treatment)
    {

        $treatment->terms_conditions = str_replace('<p>&nbsp;</p>', '', $treatment->terms_conditions);
    }

}
