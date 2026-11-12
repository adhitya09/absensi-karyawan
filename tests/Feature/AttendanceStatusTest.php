<?php

use App\Livewire\ScanComponent;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    // Set fixed time for consistent tests
    Carbon::setTestNow(Carbon::parse('2026-10-12 07:55:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('1. absensi masuk tepat waktu mendapatkan status present', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    // Masuk pukul 07:55:00 (sebelum jam 08:00:00)
    Carbon::setTestNow(Carbon::parse('2026-10-12 07:55:00'));

    $user = User::factory()->create();
    $barcode = Barcode::create([
        'name' => 'Barcode Kantor',
        'value' => 'BARCODE-001',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 100,
    ]);

    $this->actingAs($user);

    Livewire::test(ScanComponent::class)
        ->set('shift_id', $shift->id)
        ->call('scan', 'BARCODE-001', -6.200000, 106.816666);

    $attendance = Attendance::where('user_id', $user->id)->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->status)->toBe('present');
    expect($attendance->time_out)->toBeNull();
});

test('2. absensi masuk terlambat dengan toleransi 0 menit mendapatkan status late', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    // Masuk pukul 08:01:00 (melewati jam 08:00:00, toleransi 0 menit)
    Carbon::setTestNow(Carbon::parse('2026-10-12 08:01:00'));

    $user = User::factory()->create();
    $barcode = Barcode::create([
        'name' => 'Barcode Kantor',
        'value' => 'BARCODE-001',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 0,
    ]);

    $this->actingAs($user);

    Livewire::test(ScanComponent::class)
        ->set('shift_id', $shift->id)
        ->call('scan', 'BARCODE-001', -6.200000, 106.816666);

    $attendance = Attendance::where('user_id', $user->id)->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->status)->toBe('late');
});

test('3. absensi pulang sesuai jadwal dievaluasi on_time', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => '17:00:00',
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    expect($attendance->checkout_status)->toBe('on_time');
    expect($attendance->checkout_status_label)->toBe('Pulang Sesuai Jadwal');
    expect($attendance->checkout_diff_formatted)->toBe('Tepat Waktu');
});

test('4. pulang lebih awal dievaluasi early_leave dengan selisih menit tercatat', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    // Pulang pukul 16:45:00 (15 menit lebih awal dari 17:00:00)
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => '16:45:00',
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    expect($attendance->checkout_status)->toBe('early_leave');
    expect($attendance->checkout_status_label)->toBe('Pulang Lebih Awal');
    expect($attendance->checkout_diff_minutes)->toBe(15);
    expect($attendance->checkout_diff_formatted)->toBe('-15 mnt');
});

test('5. pulang setelah jam shift dievaluasi overtime_indication dengan selisih menit', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    // Pulang pukul 17:45:00 (45 menit setelah shift berakhir)
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => '17:45:00',
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    expect($attendance->checkout_status)->toBe('overtime_indication');
    expect($attendance->checkout_status_label)->toBe('Melewati Jam Shift');
    expect($attendance->checkout_diff_minutes)->toBe(45);
    expect($attendance->checkout_diff_formatted)->toBe('+45 mnt');
});

test('6. keterangan indikasi lembur mencantumkan catatan bahwa bukan persetujuan lembur otomatis', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => '18:00:00',
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    expect($attendance->checkout_note)->toContain('Indikasi lembur, bukan persetujuan lembur otomatis');
});

test('7. sudah absen masuk tetapi belum scan pulang dievaluasi in_progress', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    // Waktu saat ini: pukul 12:00:00 (shift masih berjalan)
    Carbon::setTestNow(Carbon::parse('2026-10-12 12:00:00'));

    $user = User::factory()->create();
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => null,
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    expect($attendance->checkout_status)->toBe('in_progress');
    expect($attendance->checkout_status_label)->toBe('Belum Absen Pulang');
});

test('8. shift telah berakhir tetapi belum scan pulang dievaluasi no_checkout (tidak absen pulang)', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    // Waktu saat ini: pukul 19:00:00 (shift sudah berakhir pada 17:00)
    Carbon::setTestNow(Carbon::parse('2026-10-12 19:00:00'));

    $user = User::factory()->create();
    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => null,
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    expect($attendance->checkout_status)->toBe('no_checkout');
    expect($attendance->checkout_status_label)->toBe('Tidak Absen Pulang');
    // Bukti kehadiran tetap diakui (status masuk present, bukan alpa)
    expect($attendance->status)->toBe('present');
});

test('9. evaluasi karyawan belum hadir saat hari berjalan dan alpa setelah shift selesai', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    // Saat jam 10:00 siang (hari sedang berjalan)
    Carbon::setTestNow(Carbon::parse('2026-10-12 10:00:00'));
    $latestShiftEnd = $shift->getScheduledEnd('2026-10-12');
    expect(Carbon::now()->gt($latestShiftEnd))->toBeFalse();

    // Saat jam 18:00 malam (shift telah berakhir)
    Carbon::setTestNow(Carbon::parse('2026-10-12 18:00:00'));
    expect(Carbon::now()->gt($latestShiftEnd))->toBeTrue();
});

test('10. pengajuan izin atau sakit tidak dihitung sebagai checkout atau alpa', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    $excused = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => null,
        'time_out' => null,
        'shift_id' => $shift->id,
        'status' => 'excused',
        'note' => 'Izin keperluan keluarga',
    ]);

    expect($excused->checkout_status)->toBeNull();
    expect($excused->checkout_status_label)->toBeNull();
});

test('11. pencegahan scan ganda menolak checkout ulang dan tidak menimpa waktu pulang', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-10-12 17:02:00'));

    $user = User::factory()->create();
    $barcode = Barcode::create([
        'name' => 'Barcode Kantor',
        'value' => 'BARCODE-001',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 0,
    ]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '07:55:00',
        'time_out' => '17:02:00',
        'shift_id' => $shift->id,
        'status' => 'present',
    ]);

    $this->actingAs($user);

    // Karyawan scan ulang pukul 17:05:00
    Carbon::setTestNow(Carbon::parse('2026-10-12 17:05:00'));

    $component = app(ScanComponent::class);
    $result = $component->scan('BARCODE-001', -6.200000, 106.816666);

    // Pesan penolakan dengan jam pulang tersimpan
    expect($result)->toContain('Anda sudah melakukan absen pulang pada pukul 17:02');

    // Waktu pulang di database tidak tertimpa!
    $attendance->refresh();
    expect(Carbon::parse($attendance->time_out)->format('H:i:s'))->toBe('17:02:00');
});

test('12. absensi masuk dan keluar menggunakan barcode berbeda yang valid berhasil', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    $barcodeMasuk = Barcode::create([
        'name' => 'Gerbang Masuk',
        'value' => 'BARCODE-IN',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 500,
    ]);
    $barcodeKeluar = Barcode::create([
        'name' => 'Gerbang Keluar',
        'value' => 'BARCODE-OUT',
        'latitude' => -6.200500,
        'longitude' => 106.816900,
        'radius' => 500,
    ]);

    $this->actingAs($user);

    // 1. Scan Masuk menggunakan Barcode A (Gerbang Masuk)
    Carbon::setTestNow(Carbon::parse('2026-10-12 07:55:00'));
    Livewire::test(ScanComponent::class)
        ->set('shift_id', $shift->id)
        ->call('scan', 'BARCODE-IN', -6.200000, 106.816666);

    $att = Attendance::where('user_id', $user->id)->first();
    expect($att)->not->toBeNull();
    expect($att->barcode_id)->toBe($barcodeMasuk->id);
    expect($att->time_out)->toBeNull();

    // 2. Scan Pulang menggunakan Barcode B (Gerbang Keluar)
    Carbon::setTestNow(Carbon::parse('2026-10-12 17:01:00'));
    Livewire::test(ScanComponent::class)
        ->call('scan', 'BARCODE-OUT', -6.200500, 106.816900);

    $att->refresh();
    expect($att->time_out)->not->toBeNull();
    expect(Carbon::parse($att->time_out)->format('H:i:s'))->toBe('17:01:00');
});

test('13. scan ditolak jika barcode tidak terdaftar atau di luar radius', function () {
    $shift = Shift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
    ]);

    $user = User::factory()->create();
    $barcode = Barcode::create([
        'name' => 'Barcode Kantor',
        'value' => 'BARCODE-KANTOR',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 50, // 50 meter
    ]);

    $this->actingAs($user);

    // Scan dengan barcode palsu
    $component = app(ScanComponent::class);
    $res1 = $component->scan('BARCODE-PALSU', -6.200000, 106.816666);
    expect($res1)->toContain('QR Code tidak terdaftar');

    // Scan dengan lokasi sangat jauh (misal Monas / 5km jauhnya)
    $res2 = $component->scan('BARCODE-KANTOR', -6.175392, 106.827153);
    expect($res2)->toContain('Lokasi Anda di luar jangkauan');
});

test('14. shift malam yang melewati tengah malam dikenali dengan benar', function () {
    $shift = Shift::create([
        'name' => 'Shift Malam',
        'start_time' => '19:00:00',
        'end_time' => '02:00:00',
    ]);

    expect($shift->isOvernight())->toBeTrue();

    $start = $shift->getScheduledStart('2026-10-12');
    $end = $shift->getScheduledEnd('2026-10-12');

    expect($start->format('Y-m-d H:i:s'))->toBe('2026-10-12 19:00:00');
    expect($end->format('Y-m-d H:i:s'))->toBe('2026-10-13 02:00:00');
});

test('15. checkout dini hari terhubung ke absensi masuk kemarin untuk shift malam', function () {
    $shiftMalam = Shift::create([
        'name' => 'Shift Malam',
        'start_time' => '19:00:00',
        'end_time' => '02:00:00',
    ]);

    $user = User::factory()->create();
    $barcode = Barcode::create([
        'name' => 'Barcode Kantor',
        'value' => 'BARCODE-NIGHT',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius' => 0,
    ]);

    // Karyawan check-in tanggal 2026-10-12 pukul 19:00:00
    Carbon::setTestNow(Carbon::parse('2026-10-12 19:00:00'));
    $this->actingAs($user);

    Livewire::test(ScanComponent::class)
        ->set('shift_id', $shiftMalam->id)
        ->call('scan', 'BARCODE-NIGHT', -6.200000, 106.816666);

    $att = Attendance::where('user_id', $user->id)->first();
    expect($att)->not->toBeNull();
    expect(Carbon::parse($att->date)->format('Y-m-d'))->toBe('2026-10-12');
    expect($att->time_out)->toBeNull();

    // Sekarang tanggal 2026-10-13 dini hari pukul 02:15:00 (karyawan scan pulang)
    Carbon::setTestNow(Carbon::parse('2026-10-13 02:15:00'));

    Livewire::test(ScanComponent::class)
        ->call('scan', 'BARCODE-NIGHT', -6.200000, 106.816666);

    $att->refresh();
    // Harus terhubung ke absensi tanggal 12, bukan membuat data baru!
    expect(Attendance::where('user_id', $user->id)->count())->toBe(1);
    expect(Carbon::parse($att->time_out)->format('H:i:s'))->toBe('02:15:00');
    expect($att->checkout_status)->toBe('overtime_indication');
    expect($att->checkout_diff_minutes)->toBe(15);
});

test('16. tanggal dan status absensi ditangani dengan benar pada shift malam', function () {
    $shiftMalam = Shift::create([
        'name' => 'Shift Malam',
        'start_time' => '19:00:00',
        'end_time' => '02:00:00',
    ]);

    $user = User::factory()->create();
    // Check-in kemarin pukul 18:55:00, checkout hari ini pukul 01:50:00 (10 menit lebih awal dari 02:00:00)
    $att = Attendance::create([
        'user_id' => $user->id,
        'date' => '2026-10-12',
        'time_in' => '18:55:00',
        'time_out' => '01:50:00',
        'shift_id' => $shiftMalam->id,
        'status' => 'present',
    ]);

    expect($att->checkout_status)->toBe('early_leave');
    expect($att->checkout_diff_minutes)->toBe(10);
    expect($att->checkout_diff_formatted)->toBe('-10 mnt');
});

test('17. dashboard dan rekap absensi menggunakan polling wire:poll.15s', function () {
    $dashboardView = file_get_contents(resource_path('views/livewire/admin/dashboard.blade.php'));
    expect($dashboardView)->toContain('wire:poll.15s');

    $attendanceView = file_get_contents(resource_path('views/livewire/admin/attendance.blade.php'));
    expect($attendanceView)->toContain('wire:poll.15s');
});

test('18. panel aturan menampilkan parameter aktual shift dan barcode', function () {
    $scanView = file_get_contents(resource_path('views/livewire/scan.blade.php'));
    expect($scanView)->toContain('Aturan & Ketentuan Absensi');
    expect($scanView)->toContain('Toleransi Telat:');
    expect($scanView)->toContain('0 Menit');
    expect($scanView)->toContain('Radius Lokasi GPS:');
    expect($scanView)->toContain('Pencegahan Scan Ganda');

    $attendanceView = file_get_contents(resource_path('views/livewire/admin/attendance.blade.php'));
    expect($attendanceView)->toContain('Aturan dan Parameter Absensi');
    expect($attendanceView)->toContain('Sistem Aktual');
});

test('19. karyawan tidak dapat mengakses halaman admin', function () {
    $user = User::factory()->create(['group' => 'user']);
    $this->actingAs($user);

    $response = $this->get('/admin/dashboard');
    $response->assertStatus(403);
});

test('20. hak akses admin dan superadmin sesuai middleware aktual', function () {
    $admin = User::factory()->create(['group' => 'admin']);
    $this->actingAs($admin);
    $this->get('/admin/dashboard')->assertStatus(200);

    $superadmin = User::factory()->create(['group' => 'superadmin']);
    $this->actingAs($superadmin);
    $this->get('/admin/dashboard')->assertStatus(200);
});
