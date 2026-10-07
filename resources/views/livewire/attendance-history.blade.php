<div class="space-y-5">
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce

  <!-- Header & Month Filter Controls -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
    <div>
      <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">
        Riwayat Presensi Karyawan
      </h3>
      <p class="text-xs text-slate-400 font-medium mt-0.5">
        Klik pada tanggal kehadiran untuk melihat koordinat lokasi & surat izin
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <x-label for="month_filter" value="Bulan:" class="text-xs font-semibold shrink-0"></x-label>
      <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" class="text-xs sm:text-sm py-1.5" />
    </div>
  </div>

  <!-- Main Content: Calendar + KPI Summary Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
    
    <!-- Calendar Card (8 Cols on Desktop) -->
    <div class="lg:col-span-8 flex-card p-3.5 sm:p-6 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
      <div class="w-full">
        <!-- Day of Week Headers -->
        <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-2 text-center">
          @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $idx => $day)
            <div class="py-1.5 text-[10px] sm:text-xs font-bold uppercase tracking-wider {{ $idx === 0 ? 'text-rose-500' : ($idx === 5 ? 'text-emerald-500' : 'text-slate-400') }}">
              {{ $day }}
            </div>
          @endforeach
        </div>

        <!-- Date Cells Grid -->
        <div class="grid grid-cols-7 gap-1 sm:gap-2 text-center">
          @if ($start->dayOfWeek !== 0)
            @foreach (range(1, $start->dayOfWeek) as $i)
              <div class="aspect-square sm:aspect-auto sm:h-14 rounded-xl sm:rounded-2xl border border-slate-100 dark:border-slate-800/50 bg-slate-50/40 dark:bg-slate-900/30 opacity-40">
              </div>
            @endforeach
          @endif

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
                      $shortStatus = 'Hadir';
                      $cellStyle = 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50';
                      $presentCount++;
                      break;
                  case 'late':
                      $shortStatus = 'Telat';
                      $cellStyle = 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/50';
                      $lateCount++;
                      break;
                  case 'excused':
                      $shortStatus = 'Izin';
                      $cellStyle = 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/50';
                      $excusedCount++;
                      break;
                  case 'sick':
                      $shortStatus = 'Sakit';
                      $cellStyle = 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/50';
                      $sickCount++;
                      break;
                  case 'absent':
                      $shortStatus = 'Alpa';
                      $cellStyle = 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/50';
                      $absentCount++;
                      break;
                  default:
                      $shortStatus = '-';
                      $cellStyle = 'bg-slate-50/80 text-slate-400 border-slate-100 dark:bg-slate-800/40 dark:text-slate-500 dark:border-slate-800/60';
                      break;
              }

              $hasDetail = $attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']);
            @endphp

            @if ($hasDetail)
              <button type="button"
                wire:click="show({{ $attendance['id'] }})"
                onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})"
                class="{{ $cellStyle }} aspect-square sm:aspect-auto sm:h-14 p-1 rounded-xl sm:rounded-2xl border flex flex-col items-center justify-center transition-all duration-150 hover:shadow-md hover:scale-[1.03] active:scale-95 cursor-pointer focus:outline-none">
                <span class="text-xs sm:text-sm font-bold leading-none {{ $date->isSunday() ? 'text-rose-600 dark:text-rose-400' : '' }}">
                  {{ $date->format('j') }}
                </span>
                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase mt-0.5 sm:mt-1 truncate max-w-full">
                  {{ $shortStatus }}
                </span>
              </button>
            @else
              <div class="{{ $cellStyle }} aspect-square sm:aspect-auto sm:h-14 p-1 rounded-xl sm:rounded-2xl border flex flex-col items-center justify-center">
                <span class="text-xs sm:text-sm font-bold leading-none {{ $date->isSunday() ? 'text-rose-600 dark:text-rose-400' : '' }}">
                  {{ $date->format('j') }}
                </span>
                <span class="text-[9px] sm:text-[10px] font-medium uppercase mt-0.5 sm:mt-1 truncate max-w-full">
                  {{ $shortStatus }}
                </span>
              </div>
            @endif
          @endforeach

          @if ($end->dayOfWeek !== 6)
            @foreach (range(5, $end->dayOfWeek) as $i)
              <div class="aspect-square sm:aspect-auto sm:h-14 rounded-xl sm:rounded-2xl border border-slate-100 dark:border-slate-800/50 bg-slate-50/40 dark:bg-slate-900/30 opacity-40"></div>
            @endforeach
          @endif
        </div>
      </div>
    </div>

    <!-- Summary KPI Cards (4 Cols on Desktop, 2x2 on Mobile) -->
    <div class="lg:col-span-4 flex flex-col gap-3 sm:gap-4">
      <div class="grid grid-cols-2 gap-3 sm:gap-4">
        
        <!-- Total Hadir -->
        <div class="flex-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 shadow-soft">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] sm:text-xs uppercase font-bold tracking-wider text-emerald-700 dark:text-emerald-400">Hadir</span>
            <div class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
              <x-heroicon-o-check class="w-4 h-4" />
            </div>
          </div>
          <h4 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">{{ $presentCount + $lateCount }}</h4>
          <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 mt-1 block">Telat: {{ $lateCount }} kali</span>
        </div>

        <!-- Izin -->
        <div class="flex-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60 shadow-soft">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] sm:text-xs uppercase font-bold tracking-wider text-blue-700 dark:text-blue-400">Izin</span>
            <div class="w-7 h-7 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
              <x-heroicon-o-envelope-open class="w-4 h-4" />
            </div>
          </div>
          <h4 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">{{ $excusedCount }}</h4>
          <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-400 mt-1 block">Surat Izin</span>
        </div>

        <!-- Sakit -->
        <div class="flex-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-purple-50/80 dark:bg-purple-950/40 border border-purple-200/80 dark:border-purple-800/60 shadow-soft">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] sm:text-xs uppercase font-bold tracking-wider text-purple-700 dark:text-purple-400">Sakit</span>
            <div class="w-7 h-7 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center">
              <x-heroicon-o-document-text class="w-4 h-4" />
            </div>
          </div>
          <h4 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">{{ $sickCount }}</h4>
          <span class="text-[11px] font-semibold text-purple-700 dark:text-purple-400 mt-1 block">Surat Dokter</span>
        </div>

        <!-- Absen / Alpa -->
        <div class="flex-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/60 shadow-soft">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] sm:text-xs uppercase font-bold tracking-wider text-rose-700 dark:text-rose-400">Alpa</span>
            <div class="w-7 h-7 rounded-xl bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
              <x-heroicon-o-x-mark class="w-4 h-4" />
            </div>
          </div>
          <h4 class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-white">{{ $absentCount }}</h4>
          <span class="text-[11px] font-semibold text-rose-700 dark:text-rose-400 mt-1 block">Tanpa Keterangan</span>
        </div>

      </div>

      <!-- Quick Action Card -->
      <a href="{{ route('apply-leave') }}" class="group block">
        <div class="flex items-center justify-between p-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md shadow-indigo-600/20 hover:from-indigo-500 hover:to-indigo-600 transition-all duration-200">
          <div class="space-y-0.5">
            <span class="text-xs font-bold block">Perlu Mengajukan Izin?</span>
            <span class="text-[11px] text-indigo-100">Klik di sini untuk mengisi formulir izin & sakit</span>
          </div>
          <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0 group-hover:translate-x-1 transition-transform">
            <x-heroicon-o-arrow-right class="w-4 h-4" />
          </div>
        </div>
      </a>
    </div>

  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
