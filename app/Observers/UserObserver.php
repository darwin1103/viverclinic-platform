<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\ContractedTreatment;
use App\Models\StaffProfile;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // La lógica de referidos se movió a ContractedTreatmentObserver
        // para asegurar que las recompensas se den solo tras la primera compra.
    }
}
