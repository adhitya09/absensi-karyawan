<?php

namespace App\Models;

use App\ExtendedCarbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'user_id',
        'barcode_id',
        'date',
        'time_in',
        'time_out',
        'shift_id',
        'latitude',
        'longitude',
        'status',
        'note',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime:Y-m-d',
            'time_in' => 'datetime:H:i:s',
            'time_out' => 'datetime:H:i:s',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barcode()
    {
        return $this->belongsTo(Barcode::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    function getLatLngAttribute(): array|null
    {
        if (is_null($this->latitude) || is_null($this->longitude)) {
            return null;
        }
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude
        ];
    }

    /**
     * Get the scheduled start datetime for this attendance record.
     */
    public function getScheduledStartAttribute(): ?Carbon
    {
        if (!$this->shift || !$this->date) {
            return null;
        }
        return $this->shift->getScheduledStart($this->date);
    }

    /**
     * Get the scheduled end datetime for this attendance record (supports overnight shifts).
     */
    public function getScheduledEndAttribute(): ?Carbon
    {
        if (!$this->shift || !$this->date) {
            return null;
        }
        return $this->shift->getScheduledEnd($this->date);
    }

    /**
     * Get the full Carbon datetime of check-in.
     */
    public function getCheckinDateTimeAttribute(): ?Carbon
    {
        if (!$this->date || !$this->time_in) {
            return null;
        }
        $d = Carbon::parse($this->date);
        $time = $this->time_in instanceof Carbon ? $this->time_in->format('H:i:s') : (string) $this->time_in;
        return $d->setTimeFromTimeString($time);
    }

    /**
     * Get the full Carbon datetime of check-out (supports overnight shifts crossing midnight).
     */
    public function getCheckoutDateTimeAttribute(): ?Carbon
    {
        if (!$this->date || !$this->time_out) {
            return null;
        }
        $d = Carbon::parse($this->date);
        $time = $this->time_out instanceof Carbon ? $this->time_out->format('H:i:s') : (string) $this->time_out;
        if ($this->shift && $this->shift->isOvernight() && $time < $this->shift->start_time) {
            $d->addDay();
        }
        return $d->setTimeFromTimeString($time);
    }

    /**
     * Dynamic checkout status.
     * Possible values: 'early_leave', 'on_time', 'overtime_indication', 'in_progress', 'no_checkout', or null (for excused/sick).
     */
    public function getCheckoutStatusAttribute(): ?string
    {
        if (in_array($this->status, ['excused', 'sick'])) {
            return null;
        }

        if (is_null($this->time_out)) {
            $scheduledEnd = $this->scheduled_end;
            if ($scheduledEnd && Carbon::now()->gt($scheduledEnd)) {
                return 'no_checkout';
            }
            return 'in_progress';
        }

        $scheduledEnd = $this->scheduled_end;
        $checkoutAt = $this->checkout_date_time;

        if (!$scheduledEnd || !$checkoutAt) {
            return 'checked_out';
        }

        if ($checkoutAt->format('Y-m-d H:i') === $scheduledEnd->format('Y-m-d H:i')) {
            return 'on_time';
        }

        if ($checkoutAt->lt($scheduledEnd)) {
            return 'early_leave';
        }

        return 'overtime_indication';
    }

    /**
     * Indonesian readable label for checkout status.
     */
    public function getCheckoutStatusLabelAttribute(): ?string
    {
        return match ($this->checkout_status) {
            'early_leave' => 'Pulang Lebih Awal',
            'on_time' => 'Pulang Sesuai Jadwal',
            'overtime_indication' => 'Melewati Jam Shift',
            'in_progress' => 'Belum Absen Pulang',
            'no_checkout' => 'Tidak Absen Pulang',
            default => null,
        };
    }

    /**
     * Difference in minutes between checkout time and scheduled end time.
     */
    public function getCheckoutDiffMinutesAttribute(): ?int
    {
        if (is_null($this->time_out) || !$this->scheduled_end || !$this->checkout_date_time) {
            return null;
        }
        return (int) abs($this->checkout_date_time->diffInMinutes($this->scheduled_end));
    }

    /**
     * Formatted string of the difference, e.g. "-15 mnt", "+30 mnt", "Tepat Waktu".
     */
    public function getCheckoutDiffFormattedAttribute(): ?string
    {
        $status = $this->checkout_status;
        $mins = $this->checkout_diff_minutes;

        if ($status === 'early_leave' && !is_null($mins)) {
            return "-{$mins} mnt";
        }
        if ($status === 'overtime_indication' && !is_null($mins)) {
            return "+{$mins} mnt";
        }
        if ($status === 'on_time') {
            return 'Tepat Waktu';
        }
        return null;
    }

    /**
     * Badge CSS class for the checkout status.
     */
    public function getCheckoutBadgeClassAttribute(): string
    {
        return match ($this->checkout_status) {
            'early_leave' => 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50',
            'on_time' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50',
            'overtime_indication' => 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50',
            'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50',
            'no_checkout' => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50',
            default => 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400',
        };
    }

    /**
     * Detailed human-readable note regarding checkout evaluation.
     */
    public function getCheckoutNoteAttribute(): ?string
    {
        $timeOutStr = $this->time_out ? Carbon::parse($this->time_out)->format('H:i') : '-';
        $shiftEndStr = $this->shift?->end_time ?? '-';
        $mins = $this->checkout_diff_minutes;

        return match ($this->checkout_status) {
            'early_leave' => "Pulang pada {$timeOutStr}, lebih awal {$mins} menit dari jadwal selesai shift ({$shiftEndStr}).",
            'overtime_indication' => "Pulang pada {$timeOutStr}, melewati batas jadwal shift ({$shiftEndStr}) sebesar {$mins} menit (Indikasi lembur, bukan persetujuan lembur otomatis).",
            'on_time' => "Pulang pada {$timeOutStr}, tepat sesuai jadwal selesai shift ({$shiftEndStr}).",
            'in_progress' => "Karyawan sudah absen masuk dan shift masih berlangsung / belum checkout.",
            'no_checkout' => "Jadwal shift telah selesai ({$shiftEndStr}), namun karyawan tidak melakukan scan absen pulang.",
            default => null,
        };
    }

    /**
     * Find active attendance for a user, correctly handling:
     * 1. Same-day attendance records.
     * 2. Overnight shifts from previous day that cross midnight into today.
     * 3. Independent of which valid barcode is used for checkout.
     */
    public static function findActiveAttendance(int|string $userId, ?Carbon $now = null): ?Attendance
    {
        $now = $now ? $now->copy() : Carbon::now();
        $todayDate = $now->toDateString();
        $yesterdayDate = $now->copy()->subDay()->toDateString();

        // 1. Look for today's attendance
        $todayAttendance = self::where('user_id', $userId)
            ->where('date', $todayDate)
            ->with(['shift', 'barcode'])
            ->first();

        // 2. Look for yesterday's attendance to see if there is an active overnight shift
        $yesterdayAttendance = self::where('user_id', $userId)
            ->where('date', $yesterdayDate)
            ->with(['shift', 'barcode'])
            ->first();

        if ($yesterdayAttendance && $yesterdayAttendance->shift && $yesterdayAttendance->shift->isOvernight()) {
            $scheduledEnd = $yesterdayAttendance->shift->getScheduledEnd($yesterdayAttendance->date);
            // Window for checkout of overnight shift: until scheduled end + 8 hours
            if ($now->lte($scheduledEnd->copy()->addHours(8))) {
                // If yesterday's overnight shift is still not checked out, OR today has not started yet
                if (is_null($yesterdayAttendance->time_out) || is_null($todayAttendance)) {
                    return $yesterdayAttendance;
                }
            }
        }

        return $todayAttendance;
    }

    public static function filter(
        $date = null,
        $week = null,
        $month = null,
        $year = null,
        $userId = null,
        $division = null,
        $jobTitle = null,
        $education = null
    ) {
        return self::when($date, function (Builder $query) use ($date) {
            $query->where('date', Carbon::parse($date)->toDateString());
        })->when($week && !$date, function (Builder $query) use ($week) {
            $start = Carbon::parse($week)->startOfWeek();
            $end = Carbon::parse($week)->endOfWeek();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        })->when($month && !$week && !$date, function (Builder $query) use ($month) {
            $date = Carbon::parse($month);
            $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
        })->when($year && !$month && !$week && !$date, function (Builder $query) use ($year) {
            $date = Carbon::parse($year);
            $query->whereYear('date', $date->year);
        })->when($userId, function (Builder $query) use ($userId) {
            $query->where('user_id', $userId);
        })->when($division && !$userId, function (Builder $query) use ($division) {
            $query->whereHas('user', function (Builder $query) use ($division) {
                $query->where('division_id', $division);
            });
        })->when($jobTitle && !$userId, function (Builder $query) use ($jobTitle) {
            $query->whereHas('user', function (Builder $query) use ($jobTitle) {
                $query->where('job_title_id', $jobTitle);
            });
        })->when($education && !$userId, function (Builder $query) use ($education) {
            $query->whereHas('user', function (Builder $query) use ($education) {
                $query->where('education_id', $education);
            });
        });
    }

    public function attachmentUrl(): ?Attribute
    {
        if (!$this->attachment) {
            return null;
        }

        return Attribute::get(function (): string {
            if (str_contains($this->attachment, 'https://') || str_contains($this->attachment, 'http://')) {
                return $this->attachment;
            }
            return Storage::disk(config('jetstream.attachment_disk'))->url($this->attachment);
        });
    }

    public static function clearUserAttendanceCache(Authenticatable $user, Carbon $date)
    {
        if (is_null($user)) return false;
        $date = new ExtendedCarbon($date);
        $monthYear = "$date->month-$date->year";
        $week = $date->yearWeekString();
        $ymd = $date->format('Y-m-d');

        try {
            Cache::forget("attendance-$user->id-$monthYear");
            Cache::forget("attendance-$user->id-$week");
            Cache::forget("attendance-$user->id-$ymd");
            return true;
        } catch (\Throwable $_) {
            return false;
        }
    }
}
