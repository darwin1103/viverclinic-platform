<?php

namespace App\Services;

use App\Models\Appointment;
use App\Mail\AppointmentReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Send an appointment reminder notification.
     * Falls back to email if WhatsApp fails or no phone number is present.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function sendAppointmentReminder(Appointment $appointment): void
    {
        // We assume the relationship `contractedTreatment.user` is already loaded
        // as we use `with()` in the command, but we should make sure it's accessible.
        $user = $appointment->contractedTreatment?->user;

        if (!$user) {
            Log::error('Cannot send notification. No user associated with appointment.', [
                'appointment_id' => $appointment->id
            ]);
            return;
        }

        $phone = $user->phone;

        try {
            if (empty($phone)) {
                throw new \Exception('No phone number available for user ID: ' . $user->id);
            }

            // Using a generic template name 'appointment_reminder'.
            // Ensure this template exists and is approved in your Meta WhatsApp Manager.
            $this->whatsAppService->sendTemplateMessage(
                $phone,
                'appointment_reminder',
                'es',
                []
            );

            Log::info('WhatsApp appointment reminder sent successfully', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);

        } catch (Throwable $e) {
            Log::warning('Failed to send WhatsApp reminder, falling back to Email', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);

            // Fallback to email
            if (!empty($user->email)) {
                Mail::to($user->email)->queue(new AppointmentReminderMail($appointment));
                Log::info('Email appointment reminder queued as fallback', [
                    'appointment_id' => $appointment->id,
                    'user_id' => $user->id
                ]);
            } else {
                Log::error('Cannot send fallback email reminder. No email available.', [
                    'appointment_id' => $appointment->id,
                    'user_id' => $user->id
                ]);
            }
        }
    }
}
