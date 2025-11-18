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
        // El límite de tiempo es 24 horas a partir de ahora.
        $limitDate = $now->copy()->addHours(24);

        $appointmentsToUpdate = Appointment::where('status', 'Por confirmar')
            ->where('schedule', '<=', $limitDate)
            ->get();

        if ($appointmentsToUpdate->isEmpty()) {
            $this->info('No unconfirmed appointments found within the next 24 hours.');
            return;
        }

        foreach ($appointmentsToUpdate as $appointment) {
            $appointment->status = 'No asistida';
            $appointment->save();
        }

        $this->info($appointmentsToUpdate->count() . ' appointments have been marked as "No asistida".');
    }
}
