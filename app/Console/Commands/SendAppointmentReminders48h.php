<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Date;

class SendAppointmentReminders48h extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders-48';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email/whatsapp reminders for appointments scheduled between 36 and 48 hours from now.';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $now = Date::now();
        $startWindow = $now->copy()->addHours(36);
        $endWindow = $now->copy()->addHours(48);

        $appointmentsToSendReminder = Appointment::with([
            'contractedTreatment.user',
            'contractedTreatment.branch',
            'contractedTreatment.treatment'
        ])
        ->where('status', 'Por confirmar')
        ->where('notification_reminder_sent_48', false) // Evita enviar correos duplicados
        ->where('schedule', '>=', $startWindow)
        ->where('schedule', '<=', $endWindow)
        ->get();

        if ($appointmentsToSendReminder->isEmpty()) {
            $this->info('No appointments found needing a reminder.');
            return;
        }

        foreach ($appointmentsToSendReminder as $appointment) {
            $notificationService->sendAppointmentReminder($appointment);

            // Marca la notificación como enviada
            $appointment->notification_reminder_sent_48 = true;
            $appointment->save();
        }

        $this->info($appointmentsToSendReminder->count() . ' appointment reminders have been processed.');
    }
}
