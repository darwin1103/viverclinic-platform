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
        protected OpenWAService $openWAService
    ) {}

    /**
     * Send an appointment reminder notification via both WhatsApp and Email.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function sendAppointmentReminder(Appointment $appointment): void
    {
        $appointment->loadMissing([
            'contractedTreatment.user',
            'contractedTreatment.branch',
            'contractedTreatment.treatment'
        ]);

        $user = $appointment->contractedTreatment?->user;
        $branch = $appointment->contractedTreatment?->branch;
        $treatment = $appointment->contractedTreatment?->treatment;

        if (!$user) {
            Log::error('Cannot send notification. No user associated with appointment.', [
                'appointment_id' => $appointment->id
            ]);
            return;
        }

        // 1. Send Email Notification
        if (!empty($user->email)) {
            try {
                Mail::to($user->email)->queue(new AppointmentReminderMail($appointment));
                Log::info('Email appointment reminder queued successfully', [
                    'appointment_id' => $appointment->id,
                    'user_id' => $user->id
                ]);
            } catch (Throwable $e) {
                Log::error('Failed to queue email reminder', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::warning('Cannot send email reminder. No email available.', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
        }

        // 2. Send WhatsApp Notification
        if (!empty($user->phone)) {
            try {
                $fecha = $appointment->schedule->format('d/m/Y');
                $hora = $appointment->schedule->format('h:i A');
                $treatmentName = $treatment?->name ?? 'Tratamiento';
                $sessionNumber = $appointment->session_number;
                $branchName = $branch?->name ?? 'ViverClinic';
                $branchAddress = $branch?->address ?? '';
                $branchDetail = $branchName . ($branchAddress ? " ({$branchAddress})" : "");
                $confirmationUrl = route('client.contracted-treatment.index');

                $message = "¡Hola, {$user->name}! 🌟 Te recordamos tu cita de {$treatmentName} (Sesión #{$sessionNumber}) el {$fecha} a las {$hora} en nuestra sucursal de {$branchDetail}.\n\nEs súper importante que confirmes tu asistencia para asegurar tu lugar. 💖\n\nPuedes gestionar tus citas aquí: {$confirmationUrl}\n\n*Importante:* Si no confirmas tu cita, esta podría marcarse automáticamente como 'No asistida' y perderías la sesión.";

                $this->openWAService->sendTextMessage($user->phone, $message);

                Log::info('WhatsApp appointment reminder sent successfully', [
                    'appointment_id' => $appointment->id,
                    'user_id' => $user->id
                ]);
            } catch (Throwable $e) {
                Log::error('Failed to send WhatsApp reminder', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::warning('Cannot send WhatsApp reminder. No phone number available.', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * Send WhatsApp notification when a new appointment is scheduled.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function sendAppointmentScheduled(Appointment $appointment): void
    {
        $appointment->loadMissing([
            'contractedTreatment.user',
            'contractedTreatment.branch',
            'contractedTreatment.treatment'
        ]);

        $user = $appointment->contractedTreatment?->user;
        $branch = $appointment->contractedTreatment?->branch;
        $treatment = $appointment->contractedTreatment?->treatment;

        if (!$user || empty($user->phone)) {
            return;
        }

        try {
            $fecha = $appointment->schedule->format('d/m/Y');
            $hora = $appointment->schedule->format('h:i A');
            $treatmentName = $treatment?->name ?? 'Tratamiento';
            $sessionNumber = $appointment->session_number;
            $branchName = $branch?->name ?? 'ViverClinic';
            $branchAddress = $branch?->address ?? '';
            $branchDetail = $branchName . ($branchAddress ? " ({$branchAddress})" : "");

            $message = "¡Hola, {$user->name}! 🎉 Tu cita para {$treatmentName} (Sesión #{$sessionNumber}) ha sido agendada con éxito.\n\n📅 Fecha: {$fecha}\n⏰ Hora: {$hora}\n📍 Sucursal: {$branchDetail}\n\n¡Nos vemos pronto! 🌸";

            $this->openWAService->sendTextMessage($user->phone, $message);

            Log::info('WhatsApp appointment scheduled notification sent', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to send WhatsApp scheduled notification', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp notification when an appointment is confirmed.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function sendAppointmentConfirmed(Appointment $appointment): void
    {
        $appointment->loadMissing([
            'contractedTreatment.user',
            'contractedTreatment.branch',
            'contractedTreatment.treatment'
        ]);

        $user = $appointment->contractedTreatment?->user;
        $branch = $appointment->contractedTreatment?->branch;
        $treatment = $appointment->contractedTreatment?->treatment;

        if (!$user || empty($user->phone)) {
            return;
        }

        try {
            $fecha = $appointment->schedule->format('d/m/Y');
            $hora = $appointment->schedule->format('h:i A');
            $treatmentName = $treatment?->name ?? 'Tratamiento';
            $sessionNumber = $appointment->session_number;
            $branchName = $branch?->name ?? 'ViverClinic';

            $message = "¡Hola, {$user->name}! ✅ Tu cita para {$treatmentName} (Sesión #{$sessionNumber}) el {$fecha} a las {$hora} en la sucursal {$branchName} ha sido *confirmada*. ¡Te esperamos con los brazos abiertos! 🥰";

            $this->openWAService->sendTextMessage($user->phone, $message);

            Log::info('WhatsApp appointment confirmed notification sent', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to send WhatsApp confirmed notification', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp notification when an appointment is cancelled.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function sendAppointmentCancelled(Appointment $appointment): void
    {
        $appointment->loadMissing([
            'contractedTreatment.user',
            'contractedTreatment.branch',
            'contractedTreatment.treatment'
        ]);

        $user = $appointment->contractedTreatment?->user;
        $branch = $appointment->contractedTreatment?->branch;
        $treatment = $appointment->contractedTreatment?->treatment;

        if (!$user || empty($user->phone)) {
            return;
        }

        try {
            $fecha = $appointment->schedule->format('d/m/Y');
            $hora = $appointment->schedule->format('h:i A');
            $treatmentName = $treatment?->name ?? 'Tratamiento';
            $sessionNumber = $appointment->session_number;

            $message = "Hola, {$user->name}. 📌 Te informamos que tu cita para {$treatmentName} (Sesión #{$sessionNumber}) el {$fecha} a las {$hora} ha sido *cancelada*.\n\nSi fue un error o deseas reagendar, puedes hacerlo desde tu panel o contactarnos directamente. ¡Que tengas un lindo día! ✨";

            $this->openWAService->sendTextMessage($user->phone, $message);

            Log::info('WhatsApp appointment cancelled notification sent', [
                'appointment_id' => $appointment->id,
                'user_id' => $user->id
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to send WhatsApp cancelled notification', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

