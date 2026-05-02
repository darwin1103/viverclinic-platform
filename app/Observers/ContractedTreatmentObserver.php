<?php

namespace App\Observers;

use App\Models\ContractedTreatment;
use App\Models\User;
use App\Models\Referral;
use App\Models\Appointment;
use App\Models\StaffProfile;

class ContractedTreatmentObserver
{
    /**
     * Handle the ContractedTreatment "created" event.
     */
    public function created(ContractedTreatment $contractedTreatment): void
    {
        $user = User::find($contractedTreatment->user_id);
        
        // Verificamos si este usuario fue referido por alguien
        if ($user && $user->referred_by_id) {
            
            // Buscamos el registro del referido que esté en estado Pendiente
            $referral = Referral::where('referrer_id', $user->referred_by_id)
                ->where('referred_email', $user->email)
                ->where('status', 'Pendiente')
                ->first();
                
            if ($referral) {
                // Como es su primera compra (o la primera que atrapamos mientras está Pendiente), damos recompensas
                $referrer = User::find($user->referred_by_id);
                
                if ($referrer) {
                    \Illuminate\Support\Facades\Log::info("Otorgando recompensas de referido por la primera compra de {$user->name}. Referente ID: {$referrer->id}");

                    // 1. Asignar 3 sesiones gratis al paciente referente
                    $latestContract = ContractedTreatment::where('user_id', $referrer->id)->latest()->first();
                    if ($latestContract) {
                        $latestContract->increment('sessions', 3);
                    } else {
                        \Illuminate\Support\Facades\Log::warning("No se encontró ContractedTreatment para el referente {$referrer->id}. No se sumaron 3 sesiones gratis.");
                    }

                    // 2. Calcular y asignar comisión a la última empleada que atendió al paciente referente
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
                
                // 3. Cambiar estado a Completado para no volver a premiar en futuras compras
                $referral->update(['status' => 'Completado']);
            }
        }
    }
}
