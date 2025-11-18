<?php

namespace App\Traits;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use Carbon\Carbon;

trait ValidatesAppointmentSlot
{
    /**
     * The number of additional concurrent appointments allowed beyond staff capacity.
     * @var int
     */
    private int $additionalCapacity = 4;

    /**
     * Check if a specific time slot is available for a given date and branch.
     *
     * @param Carbon $date The date of the appointment.
     * @param string $time The time in 'H:i' format (24-hour).
     * @param int $branchId The ID of the branch.
     * @return bool True if the slot is available, false otherwise.
     */
    public function isSlotAvailable(Carbon $date, string $time, int $branchId): bool
    {
        // 1. Calculate staff capacity for that specific time slot.
        $staffCapacity = $this->getStaffCapacityAt($date, $time, $branchId);

        // 2. Count appointments already booked for that exact date and time at the branch.
        $bookedCount = $this->getBookedCountAt($date, $time, $branchId);

        // 3. The slot is available if the total capacity (staff + additional) is greater than what's already booked.

        // Lógica ANTERIOR (incorrecta si no hay personal)
        // return ($staffCapacity + $this->additionalCapacity) > $bookedCount;

        // Lógica NUEVA y CORRECTA
        // La disponibilidad solo existe si hay al menos un miembro del personal trabajando (staffCapacity > 0).
        // Y, además, la capacidad total debe ser mayor que las citas ya agendadas.
        return $staffCapacity > 0 && ($staffCapacity + $this->additionalCapacity) > $bookedCount;
    }

    /**
     * Calculates how many staff members are scheduled to work at a specific time.
     */
    private function getStaffCapacityAt(Carbon $date, string $time, int $branchId): int
    {
        $dayOfWeekName = $this->getDayOfWeekInSpanish($date);

        $branch = Branch::with(['staff.workSchedules' => function ($query) use ($dayOfWeekName) {
            $query->where('day_of_week', $dayOfWeekName);
        }])->find($branchId);

        if (!$branch) {
            return 0;
        }

        $capacity = 0;
        foreach ($branch->staff as $staffMember) {
            foreach ($staffMember->workSchedules as $schedule) {
                // Check if the requested time falls within the staff member's shift
                if ($time >= $schedule->start_time && $time < $schedule->end_time) {
                    $capacity++;
                    break; // Count this staff member only once and move to the next one
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
        // Create a full DateTime object for the database query
        $scheduleDateTime = $date->copy()->setTimeFromTimeString($time);

        return Appointment::whereHas('contractedTreatment', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where('schedule', $scheduleDateTime)
            ->count();
    }
}
