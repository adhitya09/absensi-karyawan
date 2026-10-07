<x-app-layout>
  <div class="space-y-6">
    <!-- Page Header matching reference screenshot -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
      <div class="flex items-center gap-3 sm:gap-3.5">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/25 shrink-0">
          <x-heroicon-o-home class="w-5 h-5 sm:w-6 sm:h-6" />
        </div>
        <div>
          <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-800 dark:text-white">Dashboard</h1>
          <p class="text-xs text-slate-400 dark:text-slate-500 font-medium line-clamp-1 sm:line-clamp-none">Monitoring absensi dan kehadiran karyawan secara realtime</p>
        </div>
      </div>

      <!-- Action buttons matching screenshot -->
      <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
        <!-- Date Dropdown Pill -->
        <div class="inline-flex items-center justify-center gap-2 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-2xl bg-white dark:bg-[#161F30] border border-slate-200/90 dark:border-slate-700/80 text-xs font-semibold text-slate-600 dark:text-slate-300 shadow-sm shrink-0">
          <x-heroicon-o-calendar-days class="w-4 h-4 text-indigo-500" />
          <span>{{ date('F Y') }}</span>
          <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-slate-400 ml-1" />
        </div>

        <!-- Download Report Pill Button matching screenshot (Green Pill!) -->
        <a href="{{ route('admin.attendances.report', ['month' => date('Y-m'), 'download' => 1]) }}" target="_blank"
          class="inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white text-xs font-semibold shadow-emerald-glow transition-all duration-200 flex-1 sm:flex-initial">
          <x-heroicon-o-arrow-down-tray class="w-4 h-4 shrink-0" />
          <span>Download Report</span>
        </a>
      </div>
    </div>

    <!-- Livewire Dashboard Component -->
    @livewire('admin.dashboard-component')
  </div>
</x-app-layout>
