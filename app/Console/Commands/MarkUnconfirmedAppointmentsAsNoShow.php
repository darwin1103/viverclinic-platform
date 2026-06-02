<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Date;

class MarkUnconfirmedAppointmentsAsNoShow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:mark-as-no-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds unconfirmed appointments with less than 24 hours to go and marks them as "No asistida"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Date::now();
        // The time limit is 24 hours from now for unconfirmed appointments.
        $limitDate = $now->copy()->addHours(24);

        // 1. Find unconfirmed appointments within the next 24 hours.
        $unconfirmedAppointments = Appointment::where('status', 'Por confirmar')
            ->where('schedule', '<=', $limitDate)
            ->get();

        $updatedCount = 0;

        foreach ($unconfirmedAppointments as $appointment) {
            $appointment->status = 'No asistida';
            $appointment->attended = false;
            $appointment->save();
            $updatedCount++;
        }

        // 2. Find past appointments that have passed and were not marked as attended/not-attended.
        $pastAppointments = Appointment::where('schedule', '<', $now)
            ->whereNull('attended')
            ->whereNotIn('status', ['No asistida', 'Cancelada'])
            ->get();

        foreach ($pastAppointments as $appointment) {
            $appointment->status = 'No asistida';
            $appointment->attended = false;
            $appointment->save();
            $updatedCount++;
        }

        $this->info($updatedCount . ' appointments have been marked as "No asistida".');
    }
}
