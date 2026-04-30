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
        \Illuminate\Support\Facades\Log::info('Ejecutando UserObserver para referidos, Referente ID: ' . $user->referred_by_id);

        if ($user->referred_by_id) {
            $referrer = User::find($user->referred_by_id);
            
            if ($referrer) {
                // Asignar 3 sesiones gratis al paciente referente
                $latestContract = ContractedTreatment::where('user_id', $referrer->id)->latest()->first();
                if ($latestContract) {
                    $latestContract->increment('sessions', 3);
                } else {
                    \Illuminate\Support\Facades\Log::warning("No se encontró ContractedTreatment para el referente {$referrer->id}. No se sumaron 3 sesiones gratis.");
                }

                // Calcula y asigna una comisión a la última empleada que atendió al paciente referente
                $lastAppointment = Appointment::whereHas('contractedTreatment', function ($query) use ($referrer) {
                        $query->where('user_id', $referrer->id);
                    })
                    ->where('attended', true)
                    ->whereNotNull('staff_user_id')
                    ->latest('schedule')
                    ->first();

                if ($lastAppointment && $lastAppointment->staff_user_id) {
                    $staffProfile = StaffProfile::where('user_id', $lastAppointment->staff_user_id)->first();
                    if ($staffProfile) {
                        $commissionAmount = 10.00;
                        \App\Models\AccountingRecord::create([
                            'branch_id' => $staffProfile->branch_id,
                            'user_id' => $lastAppointment->staff_user_id,
                            'type' => 'income',
                            'amount' => $commissionAmount,
                            'description' => 'Comisión por referido de paciente ' . $user->name,
                            'reference_id' => $user->id,
                            'reference_type' => get_class($user),
                        ]);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::info("No se encontró última cita atendida para el referente {$referrer->id}. No se pagó comisión a empleada.");
                }
            }
        }
    }
}
