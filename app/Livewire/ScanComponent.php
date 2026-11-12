<?php

namespace App\Livewire;

use App\ExtendedCarbon;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Ballen\Distical\Calculator as DistanceCalculator;
use Ballen\Distical\Entities\LatLong;
use Illuminate\Support\Carbon;

class ScanComponent extends Component
{
    public ?Attendance $attendance = null;
    public $shift_id = null;
    public $shifts = null;
    public ?array $currentLiveCoords = null;
    public string $successMsg = '';
    public bool $isAbsence = false;

    public function scan(string $barcode, $lat = null, $lng = null)
    {
        if (!is_null($lat) && !is_null($lng) && is_numeric($lat) && is_numeric($lng)) {
            $this->currentLiveCoords = [(float) $lat, (float) $lng];
        }

        if (is_null($this->currentLiveCoords)) {
            return __('Lokasi GPS tidak terbaca atau belum terdeteksi. Silakan aktifkan GPS dan izinkan akses lokasi di browser HP Anda.');
        } else if (is_null($this->shift_id)) {
            $this->shift_id = Shift::first()?->id;
            if (is_null($this->shift_id)) {
                return __('Jadwal shift belum tersedia.');
            }
        }

        /** @var Barcode */
        $barcode = Barcode::firstWhere('value', $barcode);
        if (!Auth::check() || !$barcode) {
            return 'QR Code tidak terdaftar dalam sistem.';
        }

        if ($barcode->radius > 0 && !is_null($barcode->latLng)) {
            $barcodeLocation = new LatLong($barcode->latLng['lat'], $barcode->latLng['lng']);
            $userLocation = new LatLong($this->currentLiveCoords[0], $this->currentLiveCoords[1]);
            $distance = $this->calculateDistance($userLocation, $barcodeLocation);
            if ($distance > $barcode->radius) {
                return "Lokasi Anda di luar jangkauan ({$distance}m). Batas maksimal: {$barcode->radius}m";
            }
        }

        /** @var Attendance|null */
        $existingAttendance = Attendance::findActiveAttendance(Auth::id());

        if (!$existingAttendance) {
            // Absen Masuk (Check-In)
            $attendance = $this->createAttendance($barcode);
            $formattedTime = Carbon::now()->format('H:i');
            $statusLabel = $attendance->status === 'late' ? ' (Terlambat)' : ' (Tepat Waktu)';
            $this->successMsg = "Absen Masuk Berhasil pada pukul {$formattedTime}{$statusLabel}.";
        } elseif (!is_null($existingAttendance->time_out)) {
            // Already checked out! Prevent double scan / overwrite!
            $formattedTime = Carbon::parse($existingAttendance->time_out)->format('H:i');
            return "Anda sudah melakukan absen pulang pada pukul {$formattedTime}.";
        } else {
            // Absen Pulang (Check-Out)
            $now = Carbon::now();
            $existingAttendance->update([
                'time_out' => $now->format('H:i:s'),
            ]);
            $attendance = $existingAttendance->fresh();
            $formattedTime = $now->format('H:i');
            $diffText = $attendance->checkout_diff_formatted ? " ({$attendance->checkout_status_label}: {$attendance->checkout_diff_formatted})" : " ({$attendance->checkout_status_label})";
            $this->successMsg = "Absen Pulang Berhasil pada pukul {$formattedTime}{$diffText}.";
        }

        if ($attendance) {
            $this->setAttendance($attendance);
            Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));
            return true;
        }
    }

    public function calculateDistance(LatLong $a, LatLong $b)
    {
        $distanceCalculator = new DistanceCalculator($a, $b);
        $distanceInMeter = floor($distanceCalculator->get()->asKilometres() * 1000); // convert to meters
        return $distanceInMeter;
    }

    /** @return Attendance */
    public function createAttendance(Barcode $barcode)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $timeIn = $now->format('H:i:s');
        /** @var Shift */
        $shift = Shift::find($this->shift_id);
        $scheduledStart = $shift ? $shift->getScheduledStart($date) : $now;
        // 0-minute tolerance: if time_in > scheduled start => late
        $status = $now->gt($scheduledStart) ? 'late' : 'present';
        return Attendance::create([
            'user_id' => Auth::user()->id,
            'barcode_id' => $barcode->id,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => null,
            'shift_id' => $shift?->id,
            'latitude' => doubleval($this->currentLiveCoords[0]),
            'longitude' => doubleval($this->currentLiveCoords[1]),
            'status' => $status,
            'note' => null,
            'attachment' => null,
        ]);
    }

    protected function setAttendance(Attendance $attendance)
    {
        $this->attendance = $attendance;
        $this->shift_id = $attendance->shift_id;
        $this->isAbsence = $attendance->status !== 'present' && $attendance->status !== 'late';
    }

    public function getAttendance()
    {
        if (is_null($this->attendance)) {
            return null;
        }
        return [
            'time_in' => $this->attendance?->time_in,
            'time_out' => $this->attendance?->time_out,
        ];
    }

    public function mount()
    {
        $this->shifts = Shift::all();

        /** @var Attendance|null */
        $attendance = Attendance::findActiveAttendance(Auth::id());
        if ($attendance) {
            $this->setAttendance($attendance);
        } else {
            // get closest shift from current time
            $closest = ExtendedCarbon::now()
                ->closestFromDateArray($this->shifts->pluck('start_time')->toArray());

            $closestShift = $this->shifts
                ->where(fn (Shift $shift) => $shift->start_time == $closest->format('H:i:s'))
                ->first();
            $this->shift_id = $closestShift?->id ?? $this->shifts->first()?->id;
        }
    }

    public function render()
    {
        $selectedShift = Shift::find($this->shift_id);
        $barcodes = Barcode::all();

        return view('livewire.scan', [
            'selectedShift' => $selectedShift,
            'barcodes' => $barcodes,
        ]);
    }
}
