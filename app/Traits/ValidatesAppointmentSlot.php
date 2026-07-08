<?php

namespace App\Traits;

use App\Models\Appointment;
use App\Models\GlobalSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

trait ValidatesAppointmentSlot
{
    /**
     * Check if a specific time slot is available for a given date and branch.
     *
     * @param Carbon $date The date of the appointment.
     * @param string $time The time in 'H:i' format (24-hour).
     * @param int $branchId The ID of the branch.
     * @param bool $includeSalesSlots Whether to allow using sales slots for validation.
     * @return bool True if the slot is available, false otherwise.
     */
    public function isSlotAvailable(Carbon $date, string $time, int $branchId, bool $includeSalesSlots = false): bool
    {
        // 1. Calculate global capacity for that specific time slot.
        $capacity = $this->getGlobalCapacityAt($date, $time, $branchId, $includeSalesSlots);

        // 2. Count appointments already booked for that exact date and time at the branch.
        $bookedCount = $this->getBookedCountAt($date, $time, $branchId);

        // 3. The slot is available if the total capacity is greater than what's already booked.
        return $capacity > $bookedCount;
    }

    /**
     * Calculates the capacity defined in the global schedule for a specific time.
     */
    private function getGlobalCapacityAt(Carbon $date, string $time, int $branchId, bool $includeSalesSlots): int
    {
        $dayOfWeekName = $this->getDayOfWeekInSpanish($date);

        $globalSchedules = GlobalSchedule::where('branch_id', $branchId)
            ->where('day_of_week', $dayOfWeekName)
            ->get();

        if ($globalSchedules->isEmpty()) {
            return 0;
        }

        $requestedTime = Carbon::parse($time);
        $capacity = 0;

        foreach ($globalSchedules as $schedule) {
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);

            // Validamos si la hora solicitada está dentro del bloque de horario global.
            // Para bloques de 20 minutos ej: 09:00 a 10:00, el slot 09:40 es válido, pero 10:00 ya no.
            if ($requestedTime->isBetween($startTime, $endTime, true, false)) {
                $capacity += $schedule->regular_slots;
                if ($includeSalesSlots) {
                    $capacity += $schedule->sales_slots;
                }
            }
        }

        return $capacity;
    }

    /**
     * Counts the number of appointments booked for a precise date, time, and branch.
     */
    private function getBookedCountAt(Carbon $date, string $time, int $branchId): int
    {
        $scheduleDateTime = $date->copy()->setTimeFromTimeString($time);

        return Appointment::whereHas('contractedTreatment', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where('schedule', $scheduleDateTime)
            ->count();
    }
}
