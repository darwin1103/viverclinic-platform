<?php

namespace App\Models;

use App\Traits\Treatment\AddLazyLoadingToImages;
use App\Traits\Treatment\ConvertImages;
use App\Traits\Treatment\RemoveEmptyParagraph;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Treatment extends Model
{
    use HasFactory;
    use ConvertImages;
    use AddLazyLoadingToImages;
    use RemoveEmptyParagraph;

    protected $fillable = [
        'name',
        'description',
        'active',
        'main_image',
        'sessions',
        'days_between_sessions',
        'terms_conditions',
    ];

    /**
     * The branches that offer this treatment.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_treatment')
            ->withPivot('price') // Importante para acceder al precio
            ->withTimestamps();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {

        static::creating(function (Treatment $treatment) {

            $treatment->convertImages($treatment);
            $treatment->addLazyLoadingToImages($treatment);
            $treatment->removeEmptyParagraph($treatment);

        });

        static::updating(function (Treatment $treatment) {

            $treatment->convertImages($treatment);
            $treatment->addLazyLoadingToImages($treatment);
            $treatment->removeEmptyParagraph($treatment);

        });

    }

}
