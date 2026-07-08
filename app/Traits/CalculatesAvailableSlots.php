<?php

namespace App\Traits;

use App\Models\Appointment;
use App\Models\GlobalSchedule;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

trait CalculatesAvailableSlots
{
    /**
     * Calculate available appointment slots for a given branch and date.
     *
     * @param Carbon $date The date to check for availability.
     * @param int $branchId The ID of the branch.
     * @param int $slotDurationInMinutes The duration of each appointment slot.
     * @param bool $includeSalesSlots Whether to include extra sales slots.
     * @return array
     */
    public function calculateAvailableSlots(
        Carbon $date,
        int $branchId,
        int $slotDurationInMinutes = 20,
        bool $includeSalesSlots = false
    ): array {
        // 1. Get the Spanish name for the day of the week to match the database enum.
        $dayOfWeekName = $this->getDayOfWeekInSpanish($date);

        // 2. Fetch global schedules for that day and branch.
        $globalSchedules = GlobalSchedule::where('branch_id', $branchId)
            ->where('day_of_week', $dayOfWeekName)
            ->get();

        if ($globalSchedules->isEmpty()) {
            return []; // No schedules defined for this day
        }

        // 3. Read global slot capacity from settings.
        $regularSlots = (int) Setting::get('regular_slots', 0);
        $salesSlots = (int) Setting::get('sales_slots', 0);
        $totalCapacity = $regularSlots + ($includeSalesSlots ? $salesSlots : 0);

        if ($totalCapacity <= 0) {
            return [];
        }

        // 4. Get all appointments already booked for that day at the branch.
        $bookedAppointments = $this->getBookedAppointments($date, $branchId);

        // 5. Generate all potential time slots from the schedule blocks.
        $timeSlots = $this->generateTimeSlots($globalSchedules, $slotDurationInMinutes);

        // 6. Filter the slots based on bookings.
        return $this->filterAvailableSlots($timeSlots, $bookedAppointments, $date, $regularSlots, $salesSlots, $includeSalesSlots);
    }

    /**
     * Converts a Carbon date to the corresponding Spanish day name.
     */
    private function getDayOfWeekInSpanish(Carbon $date): string
    {
        return match ($date->dayOfWeekIso) {
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
            default => '',
        };
    }

    /**
     * Fetch appointments for a specific day and branch.
     */
    private function getBookedAppointments(Carbon $date, int $branchId): Collection
    {
        return Appointment::with('contractedTreatment')
            ->whereHas('contractedTreatment', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereDate('schedule', $date)
            ->get();
    }

    /**
     * Generates all time slots based on global schedule blocks.
     * Returns an array of unique time strings (H:i format).
     */
    private function generateTimeSlots(Collection $globalSchedules, int $slotDurationInMinutes): array
    {
        $timeSlots = [];

        foreach ($globalSchedules as $schedule) {
            $period = CarbonPeriod::create(
                $schedule->start_time,
                "{$slotDurationInMinutes} minutes",
                Carbon::parse($schedule->end_time)->subMinutes($slotDurationInMinutes)
            );

            foreach ($period as $slot) {
                $time = $slot->format('H:i');
                $timeSlots[$time] = true;
            }
        }

        ksort($timeSlots);
        return array_keys($timeSlots);
    }

    /**
     * Filters the time slots to get only the available ones.
     */
    private function filterAvailableSlots(array $timeSlots, Collection $bookedAppointments, Carbon $date, int $regularSlots, int $salesSlots, bool $includeSalesSlots): array
    {
        $availableSlots = [];
        
        $uniqueBookings = $bookedAppointments->unique('contractedTreatment.user_id');
        $bookedCounts = $uniqueBookings->map(function ($appointment) {
            return Carbon::parse($appointment->schedule)->format('H:i');
        })->countBy(); 

        $totalCapacity = $regularSlots + ($includeSalesSlots ? $salesSlots : 0);

        foreach ($timeSlots as $time) {
            if ($date->isToday() && Carbon::now()->gt(Carbon::parse($time))) {
                continue; 
            }

            $booked = $bookedCounts[$time] ?? 0;

            if ($totalCapacity > $booked) {
                $availableSlots[] = [
                    'time' => Carbon::createFromFormat('H:i', $time)->isoFormat('hh:mm a'),
                    'regular' => $regularSlots,
                    'sales' => $salesSlots,
                    'booked' => $booked,
                    'available' => $totalCapacity - $booked,
                ];
            }
        }

        return $availableSlots;
    }
}
