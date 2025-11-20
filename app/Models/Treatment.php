<?php

namespace App\Models;

use App\Traits\Treatment\AddLazyLoadingToImages;
use App\Traits\Treatment\ConvertImages;
use App\Traits\Treatment\RemoveEmptyParagraph;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'price_additional_zone',
        'price_additional_mini_zone',
        'needs_report_shots',
    ];

    public static $bigZones = [
        'Muslo',
        'Media pierna',
        'Glúteos',
        'Abdomen',
        'Pecho',
        'Brazos',
        'Espalda Alta',
        'Espalda Baja',
    ];

    public static $smallZones = [
        'Bikini',
        'Axilas',
        'Facial o Barba',
        'Cuello',
        'Linea completa Abdomen',
    ];

    public static $miniZones = [
        'Vellos de los dedos pies',
        'Vellos de los dedos mano',
        'Empeine',
        'Perianal',
        'Bigote',
        'Patillas',
        'Barbilla',
        'Orejas',
        'Entre cejo',
        'Linea alba',
        'Pezones',
        'Marcación barba',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(BranchTreatment::class);
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
