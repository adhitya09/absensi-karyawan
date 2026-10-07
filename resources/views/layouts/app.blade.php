<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title ?? config('app.name', 'Absensi Karyawan') }}</title>

  <!-- Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Styles -->
  @livewireStyles

  @stack('styles')
</head>

<body class="font-sans antialiased bg-[#F4F6FC] dark:bg-[#0B0F19] text-slate-800 dark:text-slate-100 min-h-screen">
  <x-banner />

  <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen flex">
    <!-- Left Sidebar Navigation -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-x-hidden">
      <!-- Topbar Header -->
      @include('layouts.topbar')

      <!-- Page Heading (if present) -->
      @if (isset($header))
        <div class="px-3 sm:px-6 lg:px-8 pt-2 sm:pt-3 pb-2">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-3.5">
              <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/25 shrink-0">
                <x-heroicon-o-squares-2x2 class="w-5 h-5 sm:w-6 sm:h-6" />
              </div>
              <div class="min-w-0">
                {{ $header }}
              </div>
            </div>

            <!-- Header Quick Action Pills (Hidden on mobile to save vertical space) -->
            <div class="hidden sm:flex items-center gap-2 sm:gap-3">
              <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-2xl bg-white dark:bg-[#161F30] border border-slate-200/80 dark:border-slate-700/80 text-xs font-semibold text-slate-600 dark:text-slate-300 shadow-sm">
                <x-heroicon-o-calendar-days class="w-4 h-4 text-indigo-500 shrink-0" />
                <span>{{ date('F Y') }}</span>
              </div>
            </div>
          </div>
        </div>
      @endif

      <!-- Main Page Content Slot -->
      <main class="flex-1 px-3 sm:px-6 lg:px-8 pb-10 sm:pb-12">
        {{ $slot }}
      </main>
    </div>
  </div>

  <x-sigsegv-core-dumped />

  @stack('modals')

  @livewireScripts

  @stack('scripts')
</body>

</html>
