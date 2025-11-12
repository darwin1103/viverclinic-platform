<?php

namespace App\Traits;

use App\Models\Appointment;
use App\Models\Branch;
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
     * @param int $additionalCapacity An extra number of concurrent appointments allowed.
     * @return array
     */
    public function calculateAvailableSlots(
        Carbon $date,
        int $branchId,
        int $slotDurationInMinutes = 20,
        int $additionalCapacity = 4
    ): array {
        // 1. Get the Spanish name for the day of the week to match the database enum.
        $dayOfWeekName = $this->getDayOfWeekInSpanish($date);

        // 2. Fetch all staff for the branch with their work schedules for that specific day.
        // Eager loading is used for performance.
        $branch = Branch::with(['staff.workSchedules' => function ($query) use ($dayOfWeekName) {
            $query->where('day_of_week', $dayOfWeekName);
        }])->find($branchId);

        if (!$branch) {
            return []; // Or throw an exception
        }

        // 3. Get all appointments already booked for that day at the branch.
        $bookedAppointments = $this->getBookedAppointments($date, $branchId);

        // 4. Generate all potential slots and calculate the capacity for each one.
        $slotsCapacity = $this->calculateTotalCapacityPerSlot($branch->staff, $slotDurationInMinutes);

        // 5. Filter the slots based on bookings and additional capacity.
        return $this->filterAvailableSlots($slotsCapacity, $bookedAppointments, $additionalCapacity, $date);
    }

    /**
     * Converts a Carbon date to the corresponding Spanish day name.
     */
    private function getDayOfWeekInSpanish(Carbon $date): string
    {
        // Carbon's dayOfWeekIso returns 1 for Monday, 7 for Sunday.
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
        return Appointment::whereHas('contractedTreatment', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereDate('schedule', $date)
            ->get()
            ->map(function ($appointment) {
                // We only care about the time for checking availability
                return Carbon::parse($appointment->schedule)->format('H:i');
            });
    }

    /**
     * Generates all time slots based on staff work schedules and calculates how many staff members are available for each slot.
     */
    private function calculateTotalCapacityPerSlot(Collection $staffCollection, int $slotDurationInMinutes): array
    {
        $slotsCapacity = [];

        foreach ($staffCollection as $staff) {
            foreach ($staff->workSchedules as $schedule) {
                $period = CarbonPeriod::create(
                    $schedule->start_time,
                    "{$slotDurationInMinutes} minutes",
                    // The period should not include the end_time itself as a start of a new slot.
                    Carbon::parse($schedule->end_time)->subMinutes($slotDurationInMinutes)
                );

                foreach ($period as $slot) {
                    $time = $slot->format('H:i');
                    if (!isset($slotsCapacity[$time])) {
                        $slotsCapacity[$time] = 0;
                    }
                    $slotsCapacity[$time]++; // Increment capacity for this slot
                }
            }
        }

        return $slotsCapacity;
    }

    /**
     * Filters the total capacity array to get only the available slots.
     */
    private function filterAvailableSlots(array $slotsCapacity, Collection $bookedAppointments, int $additionalCapacity, Carbon $date): array
    {
        $availableSlots = [];
        $bookedCounts = $bookedAppointments->countBy(); // Counts occurrences of each time string, e.g., ['09:00' => 2]

        // Ensure slots are sorted chronologically
        ksort($slotsCapacity);

        foreach ($slotsCapacity as $time => $capacity) {
            // Check if the slot is in the future
            if ($date->isToday() && Carbon::now()->gt(Carbon::parse($time))) {
                continue; // Skip past slots on the current day
            }

            $booked = $bookedCounts[$time] ?? 0;

            // A slot is available if the number of staff + additional capacity is greater than the number of booked appointments.
            if (($capacity + $additionalCapacity) > $booked) {
                $availableSlots[] = Carbon::createFromFormat('H:i', $time)->isoFormat('hh:mm a');
            }
        }

        return $availableSlots;
    }
}
