<?php

namespace App\Traits;

use App\Models\Appointment;
use App\Models\GlobalSchedule;
use App\Models\Setting;
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
        // 1. Check if this time falls within a global schedule block.
        $isWithinSchedule = $this->isTimeWithinSchedule($date, $time, $branchId);

        if (!$isWithinSchedule) {
            return false;
        }

        // 2. Get global slot capacity from settings.
        $regularSlots = (int) Setting::get('regular_slots', 0);
        $salesSlots = (int) Setting::get('sales_slots', 0);
        $totalCapacity = $regularSlots + ($includeSalesSlots ? $salesSlots : 0);

        if ($totalCapacity <= 0) {
            return false;
        }

        // 3. Count appointments already booked for that exact date and time at the branch.
        $bookedCount = $this->getBookedCountAt($date, $time, $branchId);

        // 4. The slot is available if the total capacity is greater than what's already booked.
        return $totalCapacity > $bookedCount;
    }

    /**
     * Checks if a given time falls within any global schedule block for the day.
     */
    private function isTimeWithinSchedule(Carbon $date, string $time, int $branchId): bool
    {
        $dayOfWeekName = $this->getDayOfWeekInSpanish($date);

        $globalSchedules = GlobalSchedule::where('branch_id', $branchId)
            ->where('day_of_week', $dayOfWeekName)
            ->get();

        if ($globalSchedules->isEmpty()) {
            return false;
        }

        $requestedTime = Carbon::parse($time);

        foreach ($globalSchedules as $schedule) {
            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);

            if ($requestedTime->isBetween($startTime, $endTime, true, false)) {
                return true;
            }
        }

        return false;
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
            ->whereNotIn('status', ['Cancelada', 'Cancelado', 'No asistida', 'No asistió'])
            ->where('schedule', $scheduleDateTime)
            ->count();
    }
}
