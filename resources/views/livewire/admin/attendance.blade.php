@php
  use Illuminate\Support\Carbon;
  $showUserDetail = !$month || $week || $date; // is week or day filter
  $isPerDayFilter = isset($date);
@endphp
<div class="space-y-5" wire:poll.15s>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <div class="flex items-center gap-2">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">
          Data Rekap Absensi
        </h3>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Live 15s</span>
        </span>
      </div>
      <p class="text-xs text-slate-400 font-medium mt-0.5">Pantau presensi harian, mingguan, dan bulanan seluruh karyawan</p>
    </div>
    <a
      href="{{ route('admin.attendances.report', ['month' => $month, 'week' => $week, 'date' => $date, 'division' => $division, 'jobTitle' => $jobTitle]) }}"
      target="_blank"
      class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-semibold text-xs shadow-emerald-glow transition-all duration-200 w-full sm:w-auto">
      <x-heroicon-o-printer class="h-4 w-4" />
      <span>Cetak Laporan</span>
    </a>
  </div>

  <!-- Panel Aturan dan Parameter Absensi (Admin & Superadmin) -->
  <div x-data="{ openRules: false }" class="flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
    <div class="flex items-center justify-between cursor-pointer" @click="openRules = !openRules">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
          <x-heroicon-o-book-open class="w-4 h-4" />
        </div>
        <div>
          <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
            <span>Aturan dan Parameter Absensi (Sistem Aktual)</span>
            <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-full">
              {{ Auth::user()?->isSuperadmin ? 'Superadmin Mode' : 'Admin Mode' }}
            </span>
          </h4>
          <p class="text-[11px] text-slate-400">Parameter jadwal shift, toleransi keterlambatan, validasi GPS, serta definisi status kepulangan</p>
        </div>
      </div>
      <button type="button" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
        <span x-text="openRules ? 'Tutup Ringkasan' : 'Lihat Detail Aturan'"></span>
        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': openRules }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </button>
    </div>

    <div x-show="openRules" x-cloak class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
      <!-- Grid Parameter Utama -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- Card 1: Shift Aktif -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-1.5 text-xs">
          <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 text-[11px]">
            <x-heroicon-o-clock class="w-3.5 h-3.5 text-indigo-500" />
            Daftar Shift Kerja
          </span>
          <div class="space-y-1 text-[11px] text-slate-600 dark:text-slate-400">
            @if (isset($shifts) && $shifts->count())
              @foreach ($shifts as $s)
                <div class="flex items-center justify-between">
                  <span>{{ $s->name }}:</span>
                  <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">
                    {{ $s->start_time }} - {{ $s->end_time }}
                    @if ($s->isOvernight())
                      <span class="text-[10px] text-purple-500 font-sans">(Shift Malam)</span>
                    @endif
                  </span>
                </div>
              @endforeach
            @else
              <span class="text-slate-400 italic">Belum ada shift terdaftar.</span>
            @endif
          </div>
        </div>

        <!-- Card 2: Toleransi & Polling -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-1.5 text-xs">
          <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 text-[11px]">
            <x-heroicon-o-exclamation-circle class="w-3.5 h-3.5 text-amber-500" />
            Toleransi & Pembaruan
          </span>
          <div class="space-y-1 text-[11px] text-slate-600 dark:text-slate-400">
            <div class="flex items-center justify-between">
              <span>Toleransi Terlambat:</span>
              <span class="font-bold text-rose-500">0 Menit</span>
            </div>
            <div class="flex items-center justify-between">
              <span>Pembaruan Realtime:</span>
              <span class="font-bold text-emerald-600">Polling 15 Detik</span>
            </div>
            <div class="flex items-center justify-between">
              <span>Pencegahan Scan Ganda:</span>
              <span class="font-semibold text-indigo-600">Aktif (Tolak Overwrite)</span>
            </div>
          </div>
        </div>

        <!-- Card 3: Barcode & GPS -->
        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-1.5 text-xs">
          <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 text-[11px]">
            <x-heroicon-o-map-pin class="w-3.5 h-3.5 text-emerald-500" />
            Titik Barcode & Radius GPS
          </span>
          <div class="space-y-1 text-[11px] text-slate-600 dark:text-slate-400">
            @if (isset($barcodes) && $barcodes->count())
              @foreach ($barcodes as $b)
                <div class="flex items-center justify-between">
                  <span class="truncate max-w-[130px]">{{ $b->name }}:</span>
                  <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">
                    {{ $b->radius > 0 ? $b->radius . 'm' : 'Tanpa Batas' }}
                  </span>
                </div>
              @endforeach
            @else
              <span class="text-slate-400 italic">Belum ada barcode terdaftar.</span>
            @endif
          </div>
        </div>
      </div>

      <!-- Definisi Status Absensi & Kepulangan -->
      <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 text-[11px] leading-relaxed space-y-2">
        <div class="font-bold text-slate-700 dark:text-slate-300">Definisi & Kebijakan Status:</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-slate-600 dark:text-slate-400">
          <div>• <strong>Hadir:</strong> Karyawan melakukan absensi masuk tepat sebelum atau tepat jam mulai shift (toleransi 0 menit).</div>
          <div>• <strong>Terlambat:</strong> Waktu masuk melewati jam mulai shift (misal masuk 08:01 untuk shift 08:00).</div>
          <div>• <strong>Pulang Sesuai Jadwal:</strong> Melakukan checkout tepat waktu pada jam selesai shift yang berlaku.</div>
          <div>• <strong>Pulang Lebih Awal:</strong> Melakukan checkout sebelum jam selesai shift. <em>Tidak otomatis dianggap alpa/bolos</em>, selisih menit dicatat.</div>
          <div>• <strong>Melewati Jam Shift:</strong> Melakukan checkout setelah jam selesai shift. Status ini merupakan <em>indikasi lembur</em>, bukan persetujuan lembur otomatis/dibayar.</div>
          <div>• <strong>Belum Absen Pulang vs Tidak Absen Pulang:</strong> Karyawan yang belum scan pulang selama shift berlangsung berstatus <em>Belum Absen Pulang</em>. Jika jadwal shift telah berakhir dan checkout tetap kosong, berstatus <em>Tidak Absen Pulang</em> (bukan alpa/bolos karena catatan hadir terbukti ada).</div>
          <div>• <strong>Belum Hadir vs Alpa:</strong> Status <em>Belum Hadir</em> hanya berlaku pada hari berjalan sebelum jam shift usai. Setelah shift usai tanpa kehadiran/izin, tercatat sebagai <em>Alpa/Tidak Hadir</em>.</div>
          <div>• <strong>Multi-Barcode Checkout:</strong> Karyawan dapat scan masuk di Barcode A dan keluar di Barcode B selama kedua barcode terdaftar dan posisi berada dalam radius GPS valid.</div>
        </div>
      </div>

      @if (Auth::user()?->isSuperadmin)
        <!-- Tabel Khusus Superadmin: Sumber Parameter Aktual & Metadata Konfigurasi -->
        <div class="p-3 rounded-xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/80 dark:border-purple-800/60 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-purple-900 dark:text-purple-300 flex items-center gap-1.5">
              <x-heroicon-o-shield-check class="w-4 h-4 text-purple-600" />
              Tabel Sumber Parameter & Metadata Konfigurasi (Superadmin)
            </span>
            <span class="text-[10px] font-semibold text-purple-700 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/60 px-2 py-0.5 rounded-full">
              Audit Konfigurasi
            </span>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-[11px] text-left text-slate-700 dark:text-slate-300">
              <thead class="text-[10px] uppercase bg-purple-100/60 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300">
                <tr>
                  <th class="px-2.5 py-1.5 font-bold">Parameter</th>
                  <th class="px-2.5 py-1.5 font-bold">Sumber Data Aktual</th>
                  <th class="px-2.5 py-1.5 font-bold">Tipe Konfigurasi</th>
                  <th class="px-2.5 py-1.5 font-bold">Keterangan / Workflow</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-purple-100 dark:divide-purple-900/40">
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Jam Mulai Shift</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">shifts.start_time</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Dapat Diubah</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Dikonfigurasi melalui menu Master Data Shift</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Jam Selesai Shift</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">shifts.end_time</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Dapat Diubah</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Dikonfigurasi melalui menu Master Data Shift</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Toleransi Keterlambatan</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">Domain Logic (0 Menit)</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Ditetapkan Kode</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Logika evaluasi ketat toleransi 0 menit di backend</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Radius Lokasi GPS</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">barcodes.radius</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Dapat Diubah</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Dikonfigurasi per barcode di menu Barcode Kantor</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Koordinat Barcode</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">barcodes.latitude / longitude</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Dapat Diubah</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Titik acuan validasi jarak GPS Haversine</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Waktu Absensi</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">Timestamp Server (now())</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Otomatis Sistem</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Waktu dicatat server, browser client tidak dapat memanipulasi</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Status Kepulangan</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">Dynamic Accessors Model Attendance</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Ditetapkan Kode</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Dihitung otomatis tanpa menambah skema tabel database</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Penanganan Shift Malam</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">Shift::isOvernight()</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Ditetapkan Kode</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Checkout dini hari otomatis menghubungkan absensi hari kemarin</td>
                </tr>
                <tr>
                  <td class="px-2.5 py-1.5 font-medium">Interval Polling Dashboard</td>
                  <td class="px-2.5 py-1.5 font-mono text-purple-700 dark:text-purple-300">wire:poll.15s</td>
                  <td class="px-2.5 py-1.5"><span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Ditetapkan Kode</span></td>
                  <td class="px-2.5 py-1.5 text-slate-500">Diperbarui setiap 15 detik menggunakan polling Livewire</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
  </div>

  <!-- Filter Controls Card -->
  <div class="flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft space-y-3 sm:space-y-4">
    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
      Periode & Filter Karyawan
    </div>

    <!-- Row 1: Date Period Pickers -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div>
        <x-label for="month_filter" value="Per Bulan" class="text-xs mb-1" />
        <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" class="w-full text-xs sm:text-sm py-2" />
      </div>
      <div>
        <x-label for="week_filter" value="Per Minggu" class="text-xs mb-1" />
        <x-input type="week" name="week_filter" id="week_filter" wire:model.live="week" class="w-full text-xs sm:text-sm py-2" />
      </div>
      <div>
        <x-label for="day_filter" value="Per Hari" class="text-xs mb-1" />
        <x-input type="date" name="day_filter" id="day_filter" wire:model.live="date" class="w-full text-xs sm:text-sm py-2" />
      </div>
    </div>

    <!-- Row 2: Selects & Search -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 border-t border-slate-100 dark:border-slate-800">
      <div>
        <x-label for="division" value="Divisi" class="text-xs mb-1" />
        <x-select id="division" wire:model.live="division" class="w-full text-xs sm:text-sm">
          <option value="">{{ __('Select Division') }}</option>
          @foreach (App\Models\Division::all() as $_division)
            <option value="{{ $_division->id }}" {{ $_division->id == $division ? 'selected' : '' }}>
              {{ $_division->name }}
            </option>
          @endforeach
        </x-select>
      </div>
      <div>
        <x-label for="jobTitle" value="Jabatan" class="text-xs mb-1" />
        <x-select id="jobTitle" wire:model.live="jobTitle" class="w-full text-xs sm:text-sm">
          <option value="">{{ __('Select Job Title') }}</option>
          @foreach (App\Models\JobTitle::all() as $_jobTitle)
            <option value="{{ $_jobTitle->id }}" {{ $_jobTitle->id == $jobTitle ? 'selected' : '' }}>
              {{ $_jobTitle->name }}
            </option>
          @endforeach
        </x-select>
      </div>
      <div>
        <x-label for="seacrh" value="Pencarian" class="text-xs mb-1" />
        <div class="flex items-center gap-2">
          <x-input type="text" class="w-full text-xs sm:text-sm" name="search" id="seacrh" wire:model="search"
            placeholder="{{ __('Search') }} nama/nip" />
          <x-button type="button" wire:click="$refresh" wire:loading.attr="disabled" class="justify-center shrink-0">
            {{ __('Search') }}
          </x-button>
          @if ($search)
            <x-secondary-button type="button" wire:click="$set('search', '')" wire:loading.attr="disabled" class="justify-center shrink-0">
              {{ __('Reset') }}
            </x-secondary-button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Panel Aturan dan Parameter Absensi (Admin & Superadmin) -->
  <div x-data="{ expanded: false }" class="flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
    <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
          <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
        </div>
        <div>
          <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
            <span>Aturan dan Parameter Absensi</span>
            <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-full">Sistem Aktual</span>
          </h4>
          <p class="text-[11px] text-slate-400">Parameter shift, toleransi keterlambatan, GPS & pedoman evaluasi status presensi</p>
        </div>
      </div>
      <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-semibold flex items-center gap-1">
        <span x-text="expanded ? 'Tutup Aturan' : 'Lihat Detail Aturan'"></span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </button>
    </div>

    <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4 text-xs">
      <!-- Grid 1: Shifts & Barcodes -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
        <!-- Daftar Shift -->
        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-2">
          <span class="font-bold text-slate-700 dark:text-slate-200 block text-[11px] uppercase tracking-wider">
            1. Jadwal Shift Terdaftar
          </span>
          <div class="space-y-1.5">
            @forelse ($shifts as $s)
              <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-[#161F30] border border-slate-100 dark:border-slate-800">
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $s->name }}</span>
                <div class="text-right">
                  <span class="font-mono text-indigo-600 dark:text-indigo-400 font-semibold">{{ $s->start_time }} - {{ $s->end_time }}</span>
                  @if ($s->isOvernight())
                    <span class="block text-[10px] text-purple-500 font-medium">(Shift Malam / Lewat Tengah Malam)</span>
                  @endif
                </div>
              </div>
            @empty
              <span class="text-slate-400">Belum ada shift terdaftar.</span>
            @endforelse
          </div>
        </div>

        <!-- Barcode & Radius GPS -->
        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-2">
          <span class="font-bold text-slate-700 dark:text-slate-200 block text-[11px] uppercase tracking-wider">
            2. Barcode & Radius Lokasi GPS
          </span>
          <div class="space-y-1.5">
            @forelse ($barcodes as $b)
              <div class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-[#161F30] border border-slate-100 dark:border-slate-800">
                <div>
                  <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $b->name }}</span>
                  <span class="font-mono text-[10px] text-slate-400">{{ $b->latitude ?? '-' }}, {{ $b->longitude ?? '-' }}</span>
                </div>
                <span class="font-semibold px-2 py-0.5 rounded-full text-[11px] {{ $b->radius > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                  {{ $b->radius > 0 ? $b->radius . ' meter' : 'Bebas Radius (0m)' }}
                </span>
              </div>
            @empty
              <span class="text-slate-400">Belum ada barcode terdaftar.</span>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Definisi Ketentuan & Status -->
      <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-2.5 leading-relaxed text-slate-600 dark:text-slate-300">
        <span class="font-bold text-slate-700 dark:text-slate-200 block text-[11px] uppercase tracking-wider">
          3. Ketentuan & Definisi Status Presensi
        </span>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
          <div class="space-y-1.5">
            <div><strong>Toleransi Keterlambatan:</strong> <span class="font-semibold text-rose-500">0 Menit</span>. Absen masuk melewati jam mulai shift langsung berstatus <em>Terlambat</em>.</div>
            <div><strong>Pencatatan Masuk & Pulang:</strong> Scan pertama mencatat Masuk, scan kedua mencatat Pulang. Scan lanjutan setelah pulang berhasil ditolak agar waktu pulang tidak tertimpa. Karyawan dapat checkout menggunakan barcode berbeda selama barcode tersebut valid.</div>
            <div><strong>Penanganan Shift Malam:</strong> Shift yang melintasi tengah malam (contoh: 19:00 - 02:00) mengenali checkout dini hari (02:15) sebagai absen keluar untuk tanggal kemarin.</div>
          </div>
          <div class="space-y-1.5">
            <div><strong>Pulang Sesuai Jadwal:</strong> Waktu checkout tepat pada waktu selesai shift.</div>
            <div><strong>Pulang Lebih Awal:</strong> Waktu checkout sebelum jam selesai shift (selisih menit tercatat). Tidak otomatis dianggap bolos.</div>
            <div><strong>Melewati Jam Shift:</strong> Waktu checkout setelah jam selesai shift. <em>Catatan: Indikasi lembur bukan persetujuan lembur resmi/dibayar</em>.</div>
            <div><strong>Belum Pulang vs Tidak Absen Pulang:</strong> "Belum Absen Pulang" berlaku saat shift masih berlangsung; "Tidak Absen Pulang" berlaku jika shift telah berakhir namun tidak ada checkout.</div>
            <div><strong>Belum Hadir vs Alpa:</strong> "Belum Hadir" berlaku saat shift masih berjalan; "Alpa" berlaku jika seluruh jadwal shift hari ini telah berakhir dan karyawan tidak hadir.</div>
          </div>
        </div>
      </div>

      <!-- Khusus Role Superadmin: Tabel Sumber Parameter -->
      @if (Auth::user()?->isSuperadmin)
        <div class="p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold text-indigo-900 dark:text-indigo-200 text-[11px] uppercase tracking-wider flex items-center gap-1.5">
              <x-heroicon-o-shield-check class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
              <span>Khusus Superadmin: Sumber Parameter & Metadata Konfigurasi</span>
            </span>
            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold">Tingkat Akses Penuh</span>
          </div>
          <div class="overflow-x-auto rounded-xl border border-indigo-200/60 dark:border-indigo-800/60 bg-white dark:bg-[#161F30]">
            <table class="w-full text-left text-[11px] divide-y divide-indigo-100 dark:divide-slate-800">
              <thead class="bg-indigo-50/80 dark:bg-slate-800 font-bold uppercase text-slate-500">
                <tr>
                  <th class="px-3 py-2">Parameter</th>
                  <th class="px-3 py-2">Sumber Data</th>
                  <th class="px-3 py-2">Tipe Konfigurasi</th>
                  <th class="px-3 py-2">Keterangan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <tr>
                  <td class="px-3 py-2 font-semibold">Jam Mulai & Selesai Shift</td>
                  <td class="px-3 py-2 font-mono text-[10px]">shifts.start_time / end_time</td>
                  <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">Database</span></td>
                  <td class="px-3 py-2">Dapat diubah melalui menu Master Data Shift</td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold">Toleransi Terlambat</td>
                  <td class="px-3 py-2 font-mono text-[10px]">0 Menit</td>
                  <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Kode Sistem</span></td>
                  <td class="px-3 py-2">Logika tetap backend pada ScanComponent</td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold">Radius & Koordinat Barcode</td>
                  <td class="px-3 py-2 font-mono text-[10px]">barcodes.radius / latitude / longitude</td>
                  <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">Database</span></td>
                  <td class="px-3 py-2">Dapat diubah melalui menu Barcode Admin</td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold">Waktu Pencatatan Absensi</td>
                  <td class="px-3 py-2 font-mono text-[10px]">Carbon::now()</td>
                  <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Server Time</span></td>
                  <td class="px-3 py-2">Waktu server backend (mencegah manipulasi waktu HP)</td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold">Status Kepulangan</td>
                  <td class="px-3 py-2 font-mono text-[10px]">Dynamic Accessors (Attendance)</td>
                  <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-600">Model Logic</span></td>
                  <td class="px-3 py-2">Dihitung otomatis tanpa perubahan skema database</td>
                </tr>
                <tr>
                  <td class="px-3 py-2 font-semibold">Interval Polling Realtime</td>
                  <td class="px-3 py-2 font-mono text-[10px]">wire:poll.15s</td>
                  <td class="px-3 py-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-600">Livewire View</span></td>
                  <td class="px-3 py-2">Pembaruan berkala setiap 15 detik pada Dashboard & Rekap</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      @endif

    </div>
  </div>

  <!-- Mobile Swipe Hint -->
  <div class="sm:hidden flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500 mb-1 font-medium">
    <span>&larr;&rarr;</span> Geser tabel untuk melihat kolom tanggal
  </div>

  <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-800/80">
    <table class="w-full divide-y divide-slate-100 dark:divide-slate-800">
      <thead class="bg-slate-50/80 dark:bg-slate-800/60">
        <tr>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ $showUserDetail ? __('Name') : __('Name') . '/' . __('Date') }}
          </th>
          @if ($showUserDetail)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('NIP') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Division') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Job Title') }}
            </th>
            @if ($isPerDayFilter)
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                {{ __('Shift') }}
              </th>
            @endif
          @endif
          @foreach ($dates as $date)
            @php
              if (!$isPerDayFilter && $date->isSunday()) {
                  // Minggu merah
                  $textClass = 'text-red-500 dark:text-red-300';
              } elseif (!$isPerDayFilter && $date->isFriday()) {
                  // Jumat hijau
                  $textClass = 'text-green-500 dark:text-green-300';
              } else {
                  $textClass = 'text-gray-500 dark:text-gray-300';
              }
            @endphp
            <th scope="col"
              class="{{ $textClass }} text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium dark:border-gray-600">
              @if ($isPerDayFilter)
                Status
              @else
                {{ $date->format('d/m') }}
              @endif
            </th>
          @endforeach
          @if ($isPerDayFilter)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Time In') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Time Out') }}
            </th>
          @endif
          @if (!$isPerDayFilter)
            @foreach (['H', 'T', 'I', 'S', 'A'] as $_st)
              <th scope="col"
                class="text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium text-gray-500 dark:border-gray-600 dark:text-gray-300">
                {{ $_st }}
              </th>
            @endforeach
          @endif
          @if ($isPerDayFilter)
            <th scope="col" class="relative">
              <span class="sr-only">Actions</span>
            </th>
          @endif
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @php
          $class = 'cursor-pointer px-4 py-3 text-sm font-medium text-gray-900 dark:text-white';
        @endphp
        @foreach ($employees as $employee)
          @php
            $attendances = $employee->attendances;
          @endphp
          <tr wire:key="{{ $employee->id }}" class="group">
            {{-- Detail karyawan --}}
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->name }}
            </td>
            @if ($showUserDetail)
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->nip }}
              </td>
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->division?->name ?? '-' }}
              </td>
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->jobTitle?->name ?? '-' }}
              </td>
              @if ($isPerDayFilter)
                @php
                  $attendance = $employee->attendances->isEmpty() ? null : $employee->attendances->first();
                  $timeIn = $attendance ? $attendance['time_in'] : null;
                  $timeOut = $attendance ? $attendance['time_out'] : null;
                @endphp
                <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                  {{ $attendance['shift'] ?? '-' }}
                </td>
              @endif
            @endif

            {{-- Absensi --}}
            @php
              $presentCount = 0;
              $lateCount = 0;
              $excusedCount = 0;
              $sickCount = 0;
              $absentCount = 0;
            @endphp
            @foreach ($dates as $date)
              @php
                $isWeekend = $date->isWeekend();
                $attendance = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
                $status = ($attendance ?? [
                    'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
                ])['status'];
                switch ($status) {
                    case 'present':
                        $shortStatus = 'H';
                        $bgColor =
                            'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-300 dark:border-green-600';
                        $presentCount++;
                        break;
                    case 'late':
                        $shortStatus = 'T';
                        $bgColor =
                            'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-300 dark:border-amber-600';
                        $lateCount++;
                        break;
                    case 'excused':
                        $shortStatus = 'I';
                        $bgColor =
                            'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-300 dark:border-blue-600';
                        $excusedCount++;
                        break;
                    case 'sick':
                        $shortStatus = 'S';
                        $bgColor =
                            'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                        $sickCount++;
                        break;
                    case 'absent':
                        $shortStatus = 'A';
                        $bgColor =
                            'bg-red-200 dark:bg-red-800 hover:bg-red-300 dark:hover:bg-red-700 border border-red-300 dark:border-red-600';
                        $absentCount++;
                        break;
                    default:
                        $shortStatus = '-';
                        $bgColor =
                            'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                        break;
                }
              @endphp
              @if (!$isPerDayFilter && $attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
                <td
                  class="{{ $bgColor }} cursor-pointer text-center text-sm font-medium text-gray-900 dark:text-white">
                  <button class="w-full px-1 py-3" wire:click="show({{ $attendance['id'] }})"
                    onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})">
                    {{ $isPerDayFilter ? __($status) : $shortStatus }}
                  </button>
                </td>
              @else
                <td
                  class="{{ $bgColor }} text-nowrap cursor-pointer px-1 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
                  {{ $isPerDayFilter ? __($status) : $shortStatus }}
                </td>
              @endif
            @endforeach

            {{-- Waktu masuk/keluar --}}
            @if ($isPerDayFilter)
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $timeIn ?? '-' }}
              </td>
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $timeOut ?? '-' }}
              </td>
            @endif

            {{-- Total --}}
            @if (!$isPerDayFilter)
              @foreach ([$presentCount, $lateCount, $excusedCount, $sickCount, $absentCount] as $statusCount)
                <td
                  class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                  {{ $statusCount }}
                </td>
              @endforeach
            @endif

            {{-- Action --}}
            @if ($isPerDayFilter)
              @php
                $attendance = $employee->attendances->isEmpty() ? null : $employee->attendances->first();
              @endphp
              <td
                class="cursor-pointer text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:text-white dark:group-hover:bg-gray-700">
                <div class="flex items-center justify-center gap-3">
                  @if ($attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
                    <x-button type="button" wire:click="show({{ $attendance['id'] }})"
                      onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})">
                      {{ __('Detail') }}
                    </x-button>
                  @else
                    -
                  @endif
                </div>
              </td>
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if ($employees->isEmpty())
    <div class="my-2 text-center text-sm font-medium text-gray-900 dark:text-gray-100">
      Tidak ada data
    </div>
  @endif
  <div class="mt-3">
    {{ $employees->links() }}
  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
