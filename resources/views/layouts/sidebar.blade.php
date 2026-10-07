@php
  $user = Auth::user();
  $isAdmin = $user && $user->isAdmin;
@endphp

<!-- Desktop Sidebar -->
<aside
  class="hidden lg:flex flex-col shrink-0 bg-white dark:bg-[#121826] border-r border-slate-200/80 dark:border-slate-800/80 transition-all duration-300 z-30 sticky top-0 h-screen overflow-y-auto"
  :class="sidebarCollapsed ? 'w-20 px-3 py-6' : 'w-64 xl:w-72 px-6 py-6'">

  <!-- Top Brand Logo: Digital Attendance By PT Son Duct Sejahtera -->
  <div class="flex items-center justify-between pb-5 mb-2 border-b border-slate-100 dark:border-slate-800/60"
    :class="sidebarCollapsed ? 'justify-center pb-4' : 'justify-between'">
    <a href="{{ $isAdmin ? route('admin.dashboard') : route('home') }}" class="flex items-center gap-2.5 group overflow-hidden" title="Digital Attendance - PT Son Duct Sejahtera">
      <x-company-logo ::show-text="!sidebarCollapsed" size="md" />
    </a>

    <!-- Collapse Toggle Button (< / >) -->
    <button type="button" @click="sidebarCollapsed = !sidebarCollapsed"
      class="w-7 h-7 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:border-slate-300 transition shrink-0"
      :class="sidebarCollapsed ? 'mt-3 mx-auto' : ''"
      title="Toggle Sidebar">
      <x-heroicon-o-chevron-left class="w-3.5 h-3.5 transition-transform duration-300" ::class="sidebarCollapsed ? 'rotate-180' : ''" />
    </button>
  </div>

  <!-- Navigation Links -->
  <div class="flex-1 space-y-6 overflow-y-auto pr-1">

    <!-- Section: Menu -->
    <div>
      <div x-show="!sidebarCollapsed" class="px-3 mb-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
        {{ __('Menu') }}
      </div>

      <nav class="space-y-1.5">
        @if ($isAdmin)
          <!-- Dashboard -->
          @php $isActive = request()->routeIs('admin.dashboard'); @endphp
          <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Dashboard') }}">
            <x-heroicon-o-home class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Dashboard') }}</span>
          </a>

          <!-- Barcode -->
          @php $isActive = request()->routeIs('admin.barcodes*'); @endphp
          <a href="{{ route('admin.barcodes') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Barcode') }}">
            <x-heroicon-o-qr-code class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Barcode') }}</span>
          </a>

          <!-- Attendance -->
          @php $isActive = request()->routeIs('admin.attendances*') && !request()->routeIs('admin.attendances.report'); @endphp
          <a href="{{ route('admin.attendances') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Attendance') }}">
            <x-heroicon-o-calendar-days class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Attendance') }}</span>
          </a>

          <!-- Employee -->
          @php $isActive = request()->routeIs('admin.employees*'); @endphp
          <a href="{{ route('admin.employees') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Employee') }}">
            <x-heroicon-o-users class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Employee') }}</span>
          </a>

          <!-- Presensi Scan QR (Bisa digunakan Admin & User) -->
          @php $isActive = request()->routeIs('home'); @endphp
          <a href="{{ route('home') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="Presensi Scan QR">
            <x-heroicon-o-camera class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Presensi Scan QR</span>
          </a>
        @else
          <!-- User: Scan QR / Home -->
          @php $isActive = request()->routeIs('home'); @endphp
          <a href="{{ route('home') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Presensi') }}">
            <x-heroicon-o-qr-code class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Presensi Scan QR</span>
          </a>

          <!-- User: Attendance History -->
          @php $isActive = request()->routeIs('attendance-history'); @endphp
          <a href="{{ route('attendance-history') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Riwayat Absensi') }}">
            <x-heroicon-o-clock class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Riwayat Absensi</span>
          </a>

          <!-- User: Apply Leave -->
          @php $isActive = request()->routeIs('apply-leave'); @endphp
          <a href="{{ route('apply-leave') }}"
            class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Pengajuan Izin') }}">
            <x-heroicon-o-document-text class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 dark:text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Pengajuan Izin</span>
          </a>
        @endif
      </nav>
    </div>

    @if ($isAdmin)
      <!-- Section: Master Data -->
      <div x-data="{ openMaster: {{ request()->routeIs('admin.masters.*') ? 'true' : 'false' }} }">
        <div x-show="!sidebarCollapsed" class="flex items-center justify-between px-3 mb-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 cursor-pointer"
          @click="openMaster = !openMaster">
          <span>{{ __('Master Data') }}</span>
          <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
            ::class="openMaster ? 'rotate-180' : ''" />
        </div>

        <nav class="space-y-1" x-show="openMaster || sidebarCollapsed">
          <!-- Division -->
          @php $isActive = request()->routeIs('admin.masters.division'); @endphp
          <a href="{{ route('admin.masters.division') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Division') }}">
            <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Division') }}</span>
          </a>

          <!-- Job Title -->
          @php $isActive = request()->routeIs('admin.masters.job-title'); @endphp
          <a href="{{ route('admin.masters.job-title') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Job Title') }}">
            <x-heroicon-o-briefcase class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Job Title') }}</span>
          </a>

          <!-- Shift -->
          @php $isActive = request()->routeIs('admin.masters.shift'); @endphp
          <a href="{{ route('admin.masters.shift') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Shift') }}">
            <x-heroicon-o-clock class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Shift') }}</span>
          </a>

          <!-- Education -->
          @php $isActive = request()->routeIs('admin.masters.education'); @endphp
          <a href="{{ route('admin.masters.education') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Education') }}">
            <x-heroicon-o-academic-cap class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Education') }}</span>
          </a>

          <!-- Admin Management -->
          @php $isActive = request()->routeIs('admin.masters.admin'); @endphp
          <a href="{{ route('admin.masters.admin') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="{{ __('Admin') }}">
            <x-heroicon-o-shield-check class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">{{ __('Admin') }}</span>
          </a>
        </nav>
      </div>

      <!-- Section: Integrations & Reports -->
      <div x-data="{ openReports: {{ request()->routeIs('admin.import-export.*') || request()->routeIs('admin.attendances.report') ? 'true' : 'false' }} }">
        <div x-show="!sidebarCollapsed" class="flex items-center justify-between px-3 mb-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 cursor-pointer"
          @click="openReports = !openReports">
          <span>{{ __('Integrations & Tools') }}</span>
          <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
            ::class="openReports ? 'rotate-180' : ''" />
        </div>

        <nav class="space-y-1" x-show="openReports || sidebarCollapsed">
          <!-- Import/Export Karyawan -->
          @php $isActive = request()->routeIs('admin.import-export.users'); @endphp
          <a href="{{ route('admin.import-export.users') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="Import/Export Karyawan">
            <x-heroicon-o-arrows-up-down class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Import & Export User</span>
          </a>

          <!-- Import/Export Absensi -->
          @php $isActive = request()->routeIs('admin.import-export.attendances'); @endphp
          <a href="{{ route('admin.import-export.attendances') }}"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="Import/Export Absensi">
            <x-heroicon-o-document-arrow-down class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Import & Export Absensi</span>
          </a>

          <!-- Cetak Laporan -->
          @php $isActive = request()->routeIs('admin.attendances.report'); @endphp
          <a href="{{ route('admin.attendances.report', ['month' => date('Y-m')]) }}" target="_blank"
            class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium transition-all duration-200 {{ $isActive ? 'nav-pill-active' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800/50' }}"
            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
            title="Cetak Laporan">
            <x-heroicon-o-printer class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400' }}" />
            <span x-show="!sidebarCollapsed" class="truncate">Cetak Laporan</span>
          </a>
        </nav>
      </div>
    @endif

  </div>

  <!-- Bottom: Logout -->
  <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-800/80">
    <form method="POST" action="{{ route('logout') }}" x-data>
      @csrf
      <button type="button" @click.prevent="$root.submit();"
        class="w-full flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-slate-600 hover:text-rose-600 hover:bg-rose-50/70 dark:text-slate-400 dark:hover:text-rose-400 dark:hover:bg-rose-950/30 transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center px-0' : ''"
        title="{{ __('Log Out') }}">
        <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 shrink-0 text-slate-500 hover:text-rose-600 dark:text-slate-400" />
        <span x-show="!sidebarCollapsed" class="truncate">{{ __('Logout') }}</span>
      </button>
    </form>
  </div>
</aside>

<!-- Mobile Sidebar Drawer (Overlay) -->
<div x-show="sidebarOpen"
  class="fixed inset-0 z-50 lg:hidden flex"
  style="display: none;"
  x-cloak>
  <!-- Backdrop -->
  <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
    @click="sidebarOpen = false"
    x-show="sidebarOpen"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"></div>

  <!-- Offcanvas Panel -->
  <div class="relative w-72 max-w-[85vw] bg-white dark:bg-[#121826] flex flex-col p-6 z-10 shadow-2xl h-full overflow-y-auto"
    x-show="sidebarOpen"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full">

    <div class="flex items-center justify-between pb-5 mb-4 border-b border-slate-100 dark:border-slate-800">
      <a href="{{ $isAdmin ? route('admin.dashboard') : route('home') }}" class="flex items-center gap-2.5">
        <x-company-logo :show-text="true" size="md" />
      </a>
      <button @click="sidebarOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
        <x-heroicon-o-x-mark class="w-6 h-6" />
      </button>
    </div>

    <!-- Mobile Nav Content -->
    <div class="flex-1 space-y-6 overflow-y-auto">
      <div>
        <div class="px-3 mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
          {{ __('Menu') }}
        </div>
        <nav class="space-y-1.5">
          @if ($isAdmin)
            <a href="{{ route('admin.dashboard') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-home class="w-5 h-5 shrink-0" />
              <span>{{ __('Dashboard') }}</span>
            </a>
            <a href="{{ route('admin.barcodes') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('admin.barcodes*') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-qr-code class="w-5 h-5 shrink-0" />
              <span>{{ __('Barcode') }}</span>
            </a>
            <a href="{{ route('admin.attendances') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('admin.attendances*') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-calendar-days class="w-5 h-5 shrink-0" />
              <span>{{ __('Attendance') }}</span>
            </a>
            <a href="{{ route('admin.employees') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('admin.employees*') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-users class="w-5 h-5 shrink-0" />
              <span>{{ __('Employee') }}</span>
            </a>
            <a href="{{ route('home') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('home') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-camera class="w-5 h-5 shrink-0" />
              <span>Presensi Scan QR</span>
            </a>
          @else
            <a href="{{ route('home') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('home') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-qr-code class="w-5 h-5 shrink-0" />
              <span>Presensi Scan QR</span>
            </a>
            <a href="{{ route('attendance-history') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('attendance-history') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-clock class="w-5 h-5 shrink-0" />
              <span>Riwayat Absensi</span>
            </a>
            <a href="{{ route('apply-leave') }}"
              class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold {{ request()->routeIs('apply-leave') ? 'nav-pill-active' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
              <x-heroicon-o-document-text class="w-5 h-5 shrink-0" />
              <span>Pengajuan Izin</span>
            </a>
          @endif
        </nav>
      </div>

      @if ($isAdmin)
        <div>
          <div class="px-3 mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
            {{ __('Master Data') }}
          </div>
          <nav class="space-y-1">
            <a href="{{ route('admin.masters.division') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-squares-2x2 class="w-5 h-5" />
              <span>{{ __('Division') }}</span>
            </a>
            <a href="{{ route('admin.masters.job-title') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-briefcase class="w-5 h-5" />
              <span>{{ __('Job Title') }}</span>
            </a>
            <a href="{{ route('admin.masters.shift') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-clock class="w-5 h-5" />
              <span>{{ __('Shift') }}</span>
            </a>
            <a href="{{ route('admin.masters.education') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-academic-cap class="w-5 h-5" />
              <span>{{ __('Education') }}</span>
            </a>
            <a href="{{ route('admin.masters.admin') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-shield-check class="w-5 h-5" />
              <span>{{ __('Admin') }}</span>
            </a>
          </nav>
        </div>

        <div>
          <div class="px-3 mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
            {{ __('Integrations') }}
          </div>
          <nav class="space-y-1">
            <a href="{{ route('admin.import-export.users') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-arrows-up-down class="w-5 h-5" />
              <span>Import/Export Karyawan</span>
            </a>
            <a href="{{ route('admin.import-export.attendances') }}" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-document-arrow-down class="w-5 h-5" />
              <span>Import/Export Absensi</span>
            </a>
            <a href="{{ route('admin.attendances.report', ['month' => date('Y-m')]) }}" target="_blank" class="flex items-center gap-3.5 px-4 py-2.5 rounded-2xl text-sm font-medium text-slate-600 dark:text-slate-300">
              <x-heroicon-o-printer class="w-5 h-5" />
              <span>Cetak Laporan</span>
            </a>
          </nav>
        </div>
      @endif
    </div>

    <!-- Mobile Logout -->
    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
      <form method="POST" action="{{ route('logout') }}" x-data>
        @csrf
        <button type="button" @click.prevent="$root.submit();"
          class="w-full flex items-center gap-3.5 px-4 py-3 rounded-2xl text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40">
          <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" />
          <span>{{ __('Logout') }}</span>
        </button>
      </form>
    </div>
  </div>
</div>
