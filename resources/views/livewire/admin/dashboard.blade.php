@php
  use Carbon\Carbon;
  $date = Carbon::now();
  $presentRate = $employeesCount > 0 ? round(($presentCount / $employeesCount) * 100, 1) : 0;
  $lateRate = $employeesCount > 0 ? round(($lateCount / $employeesCount) * 100, 1) : 0;
  $totalAttended = $presentCount + $lateCount;
  $attendanceRate = $employeesCount > 0 ? round(($totalAttended / $employeesCount) * 100, 1) : 0;
  // Circumference of semi circle (r = 75 => pi * 75 = 235.6)
  $circumference = 235.6;
  $dashOffset = $circumference - ($circumference * (min(100, max(0, $attendanceRate)) / 100));
@endphp

<div class="space-y-6">
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce

  <!-- 1. TOP STAT CARDS (Responsive 2x2 on Mobile, 4 Cols on Desktop) -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
    
    <!-- Card 1: Total Karyawan -->
    <div class="flex-card p-3.5 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft hover:shadow-soft-lg transition-all duration-200">
      <div class="flex items-center justify-between mb-2.5 sm:mb-3">
        <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
          <x-heroicon-o-users class="w-4 h-4 sm:w-6 sm:h-6" />
        </div>
        <span class="inline-flex items-center gap-0.5 sm:gap-1 text-[10px] sm:text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full">
          <span>&uarr;</span> 100%
        </span>
      </div>
      <div>
        <span class="text-[10px] sm:text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-0.5 sm:mb-1 truncate">
          Total Karyawan
        </span>
        <div class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">
          {{ $employeesCount }}<span class="text-xs sm:text-base font-normal text-slate-400 ml-1">org</span>
        </div>
      </div>
    </div>

    <!-- Card 2: Hadir Hari Ini -->
    <div class="flex-card p-3.5 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft hover:shadow-soft-lg transition-all duration-200">
      <div class="flex items-center justify-between mb-2.5 sm:mb-3">
        <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
          <x-heroicon-o-check-circle class="w-4 h-4 sm:w-6 sm:h-6" />
        </div>
        <span class="inline-flex items-center gap-0.5 sm:gap-1 text-[10px] sm:text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full">
          <span>&uarr;</span> {{ $presentRate }}%
        </span>
      </div>
      <div>
        <span class="text-[10px] sm:text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-0.5 sm:mb-1 truncate">
          Tepat Waktu
        </span>
        <div class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">
          {{ $presentCount }}<span class="text-xs sm:text-base font-normal text-slate-400 ml-1">org</span>
        </div>
      </div>
    </div>

    <!-- Card 3: Terlambat -->
    <div class="flex-card p-3.5 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft hover:shadow-soft-lg transition-all duration-200">
      <div class="flex items-center justify-between mb-2.5 sm:mb-3">
        <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0">
          <x-heroicon-o-clock class="w-4 h-4 sm:w-6 sm:h-6" />
        </div>
        <span class="inline-flex items-center gap-0.5 sm:gap-1 text-[10px] sm:text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full">
          <span>&darr;</span> {{ $lateRate }}%
        </span>
      </div>
      <div>
        <span class="text-[10px] sm:text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-0.5 sm:mb-1 truncate">
          Terlambat
        </span>
        <div class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">
          {{ $lateCount }}<span class="text-xs sm:text-base font-normal text-slate-400 ml-1">org</span>
        </div>
      </div>
    </div>

    <!-- Card 4: Izin, Sakit & Alpa -->
    <div class="flex-card p-3.5 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft hover:shadow-soft-lg transition-all duration-200">
      <div class="flex items-center justify-between mb-2.5 sm:mb-3">
        <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-50 dark:bg-purple-950/60 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
          <x-heroicon-o-document-text class="w-4 h-4 sm:w-6 sm:h-6" />
        </div>
        <span class="inline-flex items-center gap-0.5 sm:gap-1 text-[10px] sm:text-xs font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full truncate max-w-[80px] sm:max-w-none">
          {{ $excusedCount + $sickCount }} Izin
        </span>
      </div>
      <div>
        <span class="text-[10px] sm:text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-0.5 sm:mb-1 truncate">
          Belum Hadir
        </span>
        <div class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">
          {{ $absentCount }}<span class="text-xs sm:text-base font-normal text-slate-400 ml-1">org</span>
        </div>
      </div>
    </div>

  </div>

  <!-- 2. MIDDLE SECTION (Matches 2-Column Layout in Reference Screenshot) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- LEFT COLUMN (7 Cols): Store Sessions & Sessions Over Time -->
    <div class="lg:col-span-7 space-y-6">

      <!-- Card: Online Store Sessions Style (Aktivitas Kehadiran Hari Ini) -->
      <div class="flex-card p-6 bg-white dark:bg-[#161F30] rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800/60">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
              <x-heroicon-o-chart-bar class="w-5 h-5" />
            </div>
            <h3 class="font-bold text-slate-800 dark:text-white text-base">Aktivitas Presensi Hari Ini</h3>
          </div>
          <a href="{{ route('admin.attendances') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
            View Report &rarr;
          </a>
        </div>

        <div class="flex items-center justify-between flex-wrap gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
              <x-heroicon-o-user-group class="w-7 h-7" />
            </div>
            <div>
              <span class="text-xs text-slate-400 font-medium block">Total Pegawai Masuk</span>
              <span class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $totalAttended }}</span>
            </div>
          </div>

          <div class="flex items-center gap-6">
            <div class="text-right">
              <span class="text-xs text-slate-400 font-medium block">Tepat Waktu</span>
              <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">&uarr; {{ $presentCount }}</span>
            </div>
            <div class="text-right">
              <span class="text-xs text-slate-400 font-medium block">Terlambat</span>
              <span class="text-sm font-bold text-rose-500 dark:text-rose-400">&darr; {{ $lateCount }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Sessions Over Time Style (Wave Chart + Date Pills) -->
      <div class="flex-card p-6 bg-white dark:bg-[#161F30] rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-slate-800 dark:text-white text-base">Riwayat Presensi Mingguan</h3>
          <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-xs font-medium text-slate-600 dark:text-slate-300">
            <x-heroicon-o-calendar-days class="w-3.5 h-3.5 text-indigo-500" />
            <span>{{ date('F Y') }}</span>
          </div>
        </div>

        <!-- Wave Sparkline Curve matching reference screenshot -->
        <div class="w-full h-36 relative overflow-hidden py-2">
          <svg class="w-full h-full" viewBox="0 0 500 120" fill="none" preserveAspectRatio="none">
            <defs>
              <linearGradient id="waveGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#6366f1" stop-opacity="0.3" />
                <stop offset="100%" stop-color="#6366f1" stop-opacity="0.0" />
              </linearGradient>
            </defs>
            <!-- Background Grid lines -->
            <line x1="0" y1="20" x2="500" y2="20" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-dasharray="3 3" />
            <line x1="0" y1="60" x2="500" y2="60" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-dasharray="3 3" />
            <line x1="0" y1="100" x2="500" y2="100" stroke="#f1f5f9" class="dark:stroke-slate-800" stroke-dasharray="3 3" />

            <!-- Smooth Beziers -->
            <path d="M 0,90 Q 70,30 140,80 T 280,70 T 420,30 T 500,10 L 500,120 L 0,120 Z" fill="url(#waveGradient)" />
            <path d="M 0,90 Q 70,30 140,80 T 280,70 T 420,30 T 500,10" stroke="#5046e5" stroke-width="3" stroke-linecap="round" fill="none" />
            
            <!-- Indicator Dots -->
            <circle cx="140" cy="80" r="4" fill="#ffffff" stroke="#5046e5" stroke-width="2.5" />
            <circle cx="280" cy="70" r="4" fill="#ffffff" stroke="#5046e5" stroke-width="2.5" />
            <circle cx="420" cy="30" r="5" fill="#5046e5" stroke="#ffffff" stroke-width="2" />
          </svg>
        </div>

        <!-- Date Indicator Pills matching screenshot: 21, 22, 23, 24, 25... -->
        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/80">
          <button class="w-7 h-7 rounded-full text-slate-400 hover:text-slate-600 flex items-center justify-center shrink-0">
            <x-heroicon-o-chevron-left class="w-3.5 h-3.5" />
          </button>
          
          <div class="flex items-center gap-1 sm:gap-3 text-xs font-semibold overflow-x-auto no-scrollbar py-0.5">
            @php
              $todayDay = (int) date('j');
            @endphp
            @for ($d = max(1, $todayDay - 3); $d <= min(31, $todayDay + 3); $d++)
              <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[11px] sm:text-xs transition shrink-0 {{ $d === $todayDay ? 'bg-indigo-600 text-white shadow-brand font-bold' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800' }}">
                {{ $d }}
              </span>
            @endfor
          </div>

          <button class="w-7 h-7 rounded-full text-slate-400 hover:text-slate-600 flex items-center justify-center shrink-0">
            <x-heroicon-o-chevron-right class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN (5 Cols): Promo Banner & Conversion Gauge -->
    <div class="lg:col-span-5 space-y-6">

      <!-- Promo / Notice Banner Card matching "Need More Stats? [Go Pro Now]" -->
      <div class="rounded-3xl p-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 text-white relative overflow-hidden shadow-lg shadow-indigo-500/20">
        <!-- Background subtle decorative shapes -->
        <div class="absolute -right-6 -bottom-8 w-36 h-36 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
        <div class="absolute right-4 top-4 opacity-15 pointer-events-none">
          <x-heroicon-o-sparkles class="w-20 h-20" />
        </div>

        <div class="relative z-10 space-y-3">
          <h3 class="text-xl font-extrabold tracking-tight leading-snug">
            Sistem Absensi Realtime
          </h3>
          <p class="text-xs text-indigo-100 leading-relaxed font-normal max-w-xs">
            Cetak rekap laporan kehadiran per divisi atau pantau perizinan karyawan secara cepat.
          </p>

          <div class="pt-2">
            <a href="{{ route('admin.attendances.report', ['month' => date('Y-m')]) }}" target="_blank"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-xs shadow-md shadow-emerald-950/20 transition-all duration-200">
              <x-heroicon-o-printer class="w-4 h-4" />
              <span>Cetak Laporan</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Semi-Circle Gauge Card matching "Conversion 58,19%" -->
      <div class="flex-card p-6 bg-white dark:bg-[#161F30] rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <h3 class="font-bold text-slate-800 dark:text-white text-base mb-2">Tingkat Kehadiran</h3>

        <!-- Semi-Circle Arc SVG Meter -->
        <div class="flex flex-col items-center justify-center my-3">
          <div class="relative w-56 h-28 overflow-hidden flex items-end justify-center">
            <svg class="w-56 h-56 transform -rotate-180" viewBox="0 0 180 180">
              <!-- Background grey arc -->
              <circle cx="90" cy="90" r="75"
                fill="none"
                stroke="#e2e8f0"
                class="dark:stroke-slate-800"
                stroke-width="18"
                stroke-dasharray="235.6"
                stroke-dashoffset="0" />
              
              <!-- Vibrant Indigo filled arc -->
              <circle cx="90" cy="90" r="75"
                fill="none"
                stroke="url(#arcGradient)"
                stroke-width="18"
                stroke-linecap="round"
                stroke-dasharray="235.6"
                stroke-dashoffset="{{ $dashOffset }}"
                style="transition: stroke-dashoffset 1.2s ease-out;" />
              
              <defs>
                <linearGradient id="arcGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                  <stop offset="0%" stop-color="#818cf8" />
                  <stop offset="100%" stop-color="#4f46e5" />
                </linearGradient>
              </defs>
            </svg>

            <!-- Text in Center of Arc -->
            <div class="absolute bottom-1 text-center">
              <div class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                {{ $attendanceRate }}%
              </div>
              <div class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center justify-center gap-0.5">
                <span>&uarr;</span> {{ $totalAttended }} Pegawai Hadir
              </div>
            </div>
          </div>
        </div>

        <!-- Bottom Indicators: Income & Expences Style -->
        <div class="grid grid-cols-2 gap-4 pt-4 mt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
            <div>
              <span class="text-slate-400 font-medium block">Hadir</span>
              <span class="font-bold text-slate-800 dark:text-slate-200">{{ $totalAttended }} org</span>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
            <div>
              <span class="text-slate-400 font-medium block">Alpa / Absen</span>
              <span class="font-bold text-slate-800 dark:text-slate-200">{{ $absentCount }} org</span>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>

  <!-- 3. BOTTOM SECTION: MODERN EMPLOYEE ATTENDANCE TABLE -->
  <div class="flex-card p-4 sm:p-6 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-5 pb-3 sm:pb-4 border-b border-slate-100 dark:border-slate-800/80">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Daftar Absensi Karyawan Hari Ini</h3>
        <p class="text-xs text-slate-400 font-medium mt-0.5">Daftar absensi harian seluruh karyawan aktif</p>
      </div>

      <div class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300 w-fit">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        <span>Total: {{ $employeesCount }} Karyawan</span>
      </div>
    </div>

    <!-- Mobile Swipe Hint -->
    <div class="sm:hidden flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500 mb-2 font-medium">
      <span>&larr;&rarr;</span> Geser tabel untuk melihat kolom lengkap
    </div>

    <!-- Table Container -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-800/80">
      <table class="w-full text-left divide-y divide-slate-100 dark:divide-slate-800">
        <thead class="bg-slate-50/80 dark:bg-slate-800/50 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">
          <tr>
            <th scope="col" class="px-5 py-3.5">{{ __('Name') }}</th>
            <th scope="col" class="px-4 py-3.5">{{ __('NIP') }}</th>
            <th scope="col" class="px-4 py-3.5">{{ __('Division') }}</th>
            <th scope="col" class="px-4 py-3.5">{{ __('Job Title') }}</th>
            <th scope="col" class="px-4 py-3.5">{{ __('Shift') }}</th>
            <th scope="col" class="px-4 py-3.5 text-center">{{ __('Status') }}</th>
            <th scope="col" class="px-4 py-3.5">{{ __('Time In') }}</th>
            <th scope="col" class="px-4 py-3.5">{{ __('Time Out') }}</th>
            <th scope="col" class="px-4 py-3.5 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-[#161F30] text-sm">
          @forelse ($employees as $employee)
            @php
              $attendance = $employee->attendance;
              $timeIn = $attendance ? $attendance?->time_in?->format('H:i:s') : null;
              $timeOut = $attendance ? $attendance?->time_out?->format('H:i:s') : null;
              $isWeekend = $date->isWeekend();
              $status = ($attendance ?? [
                  'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
              ])['status'];

              switch ($status) {
                  case 'present':
                      $badgeClass = 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40';
                      $statusLabel = 'Hadir';
                      break;
                  case 'late':
                      $badgeClass = 'bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40';
                      $statusLabel = 'Terlambat';
                      break;
                  case 'excused':
                      $badgeClass = 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/40';
                      $statusLabel = 'Izin';
                      break;
                  case 'sick':
                      $badgeClass = 'bg-purple-50 text-purple-600 dark:bg-purple-950/50 dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/40';
                      $statusLabel = 'Sakit';
                      break;
                  case 'absent':
                      $badgeClass = 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40';
                      $statusLabel = 'Alpa';
                      break;
                  default:
                      $badgeClass = 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400';
                      $statusLabel = '-';
                      break;
              }
            @endphp
            <tr wire:key="{{ $employee->id }}" class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
              <!-- Nama & Avatar -->
              <td class="px-5 py-4 font-semibold text-slate-800 dark:text-slate-100 text-nowrap">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-500 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                  </div>
                  <span>{{ $employee->name }}</span>
                </div>
              </td>

              <!-- NIP -->
              <td class="px-4 py-4 text-slate-500 dark:text-slate-400 font-mono text-xs">
                {{ $employee->nip ?? '-' }}
              </td>

              <!-- Division -->
              <td class="px-4 py-4 text-slate-600 dark:text-slate-300 text-nowrap">
                <span class="inline-block px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs font-medium">
                  {{ $employee->division?->name ?? '-' }}
                </span>
              </td>

              <!-- Job Title -->
              <td class="px-4 py-4 text-slate-600 dark:text-slate-300 text-nowrap">
                {{ $employee->jobTitle?->name ?? '-' }}
              </td>

              <!-- Shift -->
              <td class="px-4 py-4 text-slate-500 dark:text-slate-400 text-nowrap text-xs">
                {{ $attendance->shift?->name ?? '-' }}
              </td>

              <!-- Status Badge -->
              <td class="px-4 py-4 text-center text-nowrap">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                  {{ $statusLabel }}
                </span>
              </td>

              <!-- Time In -->
              <td class="px-4 py-4 text-slate-600 dark:text-slate-300 font-mono text-xs">
                {{ $timeIn ?? '-' }}
              </td>

              <!-- Time Out -->
              <td class="px-4 py-4 text-slate-600 dark:text-slate-300 font-mono text-xs">
                {{ $timeOut ?? '-' }}
              </td>

              <!-- Action Detail Button -->
              <td class="px-4 py-4 text-center">
                @if ($attendance && ($attendance->attachment || $attendance->note || $attendance->lat_lng))
                  <button type="button"
                    wire:click="show({{ $attendance->id }})"
                    onclick="setLocation({{ $attendance->latitude ?? 0 }}, {{ $attendance->longitude ?? 0 }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-600 dark:text-indigo-400 text-xs font-semibold transition">
                    <x-heroicon-o-eye class="w-3.5 h-3.5" />
                    <span>Detail</span>
                  </button>
                @else
                  <span class="text-slate-300 dark:text-slate-600">-</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="px-5 py-8 text-center text-slate-400 text-sm">
                Tidak ada data karyawan ditemukan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      {{ $employees->links() }}
    </div>
  </div>

  <!-- Detail Modal -->
  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
