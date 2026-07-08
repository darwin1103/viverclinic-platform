<?php

namespace App\Traits;

use App\Models\Appointment;
use App\Models\GlobalSchedule;
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

        // 3. Get all appointments already booked for that day at the branch.
        $bookedAppointments = $this->getBookedAppointments($date, $branchId);

        // 4. Generate all potential slots and calculate the capacity for each one.
        $slotsCapacity = $this->calculateTotalCapacityPerSlot($globalSchedules, $slotDurationInMinutes, $includeSalesSlots);

        // 5. Filter the slots based on bookings.
        return $this->filterAvailableSlots($slotsCapacity, $bookedAppointments, $date);
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
     * Generates all time slots based on global schedules.
     * Capacity is calculated directly from regular_slots (and sales_slots if included).
     */
    private function calculateTotalCapacityPerSlot(Collection $globalSchedules, int $slotDurationInMinutes, bool $includeSalesSlots): array
    {
        $slotsCapacity = [];

        foreach ($globalSchedules as $schedule) {
            $period = CarbonPeriod::create(
                $schedule->start_time,
                "{$slotDurationInMinutes} minutes",
                Carbon::parse($schedule->end_time)->subMinutes($slotDurationInMinutes)
            );

            $totalCapacityForBlock = $schedule->regular_slots;
            if ($includeSalesSlots) {
                $totalCapacityForBlock += $schedule->sales_slots;
            }

            foreach ($period as $slot) {
                $time = $slot->format('H:i');
                if (!isset($slotsCapacity[$time])) {
                    $slotsCapacity[$time] = [
                        'regular' => 0,
                        'sales' => 0,
                        'total' => 0,
                    ];
                }
                
                $slotsCapacity[$time]['regular'] += $schedule->regular_slots;
                $slotsCapacity[$time]['sales'] += $schedule->sales_slots;
                $slotsCapacity[$time]['total'] += $totalCapacityForBlock;
            }
        }

        // Remove slots with 0 total capacity
        foreach ($slotsCapacity as $time => $capacity) {
            if ($capacity['total'] <= 0) {
                unset($slotsCapacity[$time]);
            }
        }

        return $slotsCapacity;
    }

    /**
     * Filters the total capacity array to get only the available slots.
     */
    private function filterAvailableSlots(array $slotsCapacity, Collection $bookedAppointments, Carbon $date): array
    {
        $availableSlots = [];
        
        $uniqueBookings = $bookedAppointments->unique('contractedTreatment.user_id');
        $bookedCounts = $uniqueBookings->map(function ($appointment) {
            return Carbon::parse($appointment->schedule)->format('H:i');
        })->countBy(); 

        ksort($slotsCapacity);

        foreach ($slotsCapacity as $time => $capacity) {
            if ($date->isToday() && Carbon::now()->gt(Carbon::parse($time))) {
                continue; 
            }

            $booked = $bookedCounts[$time] ?? 0;

            if ($capacity['total'] > $booked) {
                $available = $capacity['total'] - $booked;
                $availableSlots[] = [
                    'time' => Carbon::createFromFormat('H:i', $time)->isoFormat('hh:mm a'),
                    'regular' => $capacity['regular'],
                    'sales' => $capacity['sales'],
                    'booked' => $booked,
                    'available' => $available,
                ];
            }
        }

        return $availableSlots;
    }
}
