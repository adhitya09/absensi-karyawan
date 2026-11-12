<?php

namespace App\Livewire\Traits;

use App\Models\Attendance;

trait AttendanceDetailTrait
{
    public bool $showDetail = false;
    public $currentAttendance = [];

    public function show($attendanceId)
    {
        /** @var Attendance */
        $attendance = Attendance::find($attendanceId);
        if ($attendance) {
            $this->showDetail = true;
            $this->currentAttendance = $attendance->getAttributes();
            $this->currentAttendance['name'] = $attendance->user->name;
            $this->currentAttendance['nip'] = $attendance->user->nip;
            $this->currentAttendance['address'] = $attendance->user->address;
            if ($attendance->attachment) {
                $this->currentAttendance['attachment'] = $attendance->attachment_url;
            }
            if ($attendance->barcode_id) {
                $this->currentAttendance['barcode'] = $attendance->barcode;
            }
            if ($attendance->shift_id) {
                $this->currentAttendance['shift'] = $attendance->shift;
            }
            $this->currentAttendance['checkout_status'] = $attendance->checkout_status;
            $this->currentAttendance['checkout_status_label'] = $attendance->checkout_status_label;
            $this->currentAttendance['checkout_diff_formatted'] = $attendance->checkout_diff_formatted;
            $this->currentAttendance['checkout_badge_class'] = $attendance->checkout_badge_class;
            $this->currentAttendance['checkout_note'] = $attendance->checkout_note;
        }
    }
}
