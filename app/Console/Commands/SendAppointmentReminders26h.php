<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Mail\AppointmentReminderMail;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders26h extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders-26';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminders for appointments scheduled between 24 and 26 hours from now.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Date::now();
        $startWindow = $now->copy()->addHours(24);
        $endWindow = $now->copy()->addHours(26);

        $appointmentsToSendReminder = Appointment::with([
            'contractedTreatment.user',
            'contractedTreatment.branch',
            'contractedTreatment.treatment'
        ])
        ->where('status', 'Por confirmar')
        ->where('notification_reminder_sent_26', false) // Evita enviar correos duplicados
        ->where('schedule', '>=', $startWindow)
        ->where('schedule', '<=', $endWindow)
        ->get();

        if ($appointmentsToSendReminder->isEmpty()) {
            $this->info('No appointments found needing a reminder.');
            return;
        }

        foreach ($appointmentsToSendReminder as $appointment) {
            // Se encola el correo para no afectar el rendimiento
            Mail::to($appointment->contractedTreatment->user->email)
                ->queue(new AppointmentReminderMail($appointment));

            // Marca la notificación como enviada
            $appointment->notification_reminder_sent = true;
            $appointment->save();
        }

        $this->info($appointmentsToSendReminder->count() . ' appointment reminders have been queued.');
    }
}
