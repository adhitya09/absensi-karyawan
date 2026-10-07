@php
  use Illuminate\Support\Carbon;
  $showUserDetail = !$month || $week || $date; // is week or day filter
  $isPerDayFilter = isset($date);
@endphp
<div class="space-y-5">
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">
        Data Rekap Absensi
      </h3>
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
