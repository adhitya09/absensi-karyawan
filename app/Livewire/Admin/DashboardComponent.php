<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\AttendanceDetailTrait;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class DashboardComponent extends Component
{
    use AttendanceDetailTrait;

    public function render()
    {
        $now = \Illuminate\Support\Carbon::now();
        $today = date('Y-m-d');
        $yesterday = \Illuminate\Support\Carbon::yesterday()->toDateString();

        /** @var Collection<Attendance> */
        $attendances = Attendance::where('date', $today)
            ->orWhere(function ($q) use ($yesterday) {
                $q->where('date', $yesterday)
                  ->whereHas('shift', function ($sq) {
                      $sq->whereColumn('end_time', '<', 'start_time');
                  });
            })
            ->with(['shift', 'barcode', 'user'])
            ->get();

        /** @var Collection<User> */
        $employees = User::where('group', 'user')
            ->paginate(20)
            ->through(function (User $user) use ($attendances) {
                return $user->setAttribute(
                    'attendance',
                    $attendances
                        ->where(fn (Attendance $attendance) => $attendance->user_id === $user->id)
                        ->first(),
                );
            });

        $employeesCount = User::where('group', 'user')->count();
        $presentCount = $attendances->where(fn ($attendance) => $attendance->status === 'present')->count();
        $lateCount = $attendances->where(fn ($attendance) => $attendance->status === 'late')->count();
        $excusedCount = $attendances->where(fn ($attendance) => $attendance->status === 'excused')->count();
        $sickCount = $attendances->where(fn ($attendance) => $attendance->status === 'sick')->count();

        // Checkout status breakdowns
        $earlyLeaveCount = $attendances->where(fn ($a) => $a->checkout_status === 'early_leave')->count();
        $onTimeCheckoutCount = $attendances->where(fn ($a) => $a->checkout_status === 'on_time')->count();
        $overtimeIndicationCount = $attendances->where(fn ($a) => $a->checkout_status === 'overtime_indication')->count();
        $inProgressCount = $attendances->where(fn ($a) => $a->checkout_status === 'in_progress')->count();
        $noCheckoutCount = $attendances->where(fn ($a) => $a->checkout_status === 'no_checkout')->count();

        // Evaluation of Belum Hadir vs Tidak Hadir (Alpa)
        $latestShiftEnd = \App\Models\Shift::all()->map(fn ($s) => $s->getScheduledEnd($today))->max();
        $allShiftsEnded = $latestShiftEnd ? $now->gt($latestShiftEnd) : false;
        $unaccountedCount = max(0, $employeesCount - ($presentCount + $lateCount + $excusedCount + $sickCount));

        if ($allShiftsEnded) {
            $absentCount = $unaccountedCount;
            $notYetAttendedCount = 0;
        } else {
            $notYetAttendedCount = $unaccountedCount;
            $absentCount = 0;
        }

        return view('livewire.admin.dashboard', [
            'employees' => $employees,
            'employeesCount' => $employeesCount,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $excusedCount,
            'sickCount' => $sickCount,
            'absentCount' => $absentCount,
            'notYetAttendedCount' => $notYetAttendedCount,
            'allShiftsEnded' => $allShiftsEnded,
            'earlyLeaveCount' => $earlyLeaveCount,
            'onTimeCheckoutCount' => $onTimeCheckoutCount,
            'overtimeIndicationCount' => $overtimeIndicationCount,
            'inProgressCount' => $inProgressCount,
            'noCheckoutCount' => $noCheckoutCount,
        ]);
    }
}
