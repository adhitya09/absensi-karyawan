@php
  $user = Auth::user();
  $firstName = $user ? explode(' ', trim($user->name))[0] : 'User';
@endphp

<header class="bg-transparent px-3 sm:px-6 lg:px-8 pt-3 sm:pt-5 pb-2 sm:pb-3 flex items-center justify-between gap-2 sm:gap-4 sticky top-0 z-20 backdrop-blur-md bg-[#F4F6FC]/85 dark:bg-[#0B0F19]/85 lg:static lg:bg-transparent lg:backdrop-blur-none transition-all">
  <!-- Left Side: Mobile Menu Button & Mobile Brand / Desktop Search Bar -->
  <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
    <!-- Mobile Hamburger Toggle -->
    <button type="button" @click="sidebarOpen = true"
      class="lg:hidden p-2 rounded-2xl bg-white dark:bg-[#161F30] border border-slate-200/80 dark:border-slate-700/80 text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white shadow-sm transition shrink-0"
      aria-label="Open Sidebar Menu">
      <x-heroicon-o-bars-3 class="w-5 h-5" />
    </button>

    <!-- Mobile Mini Brand Display -->
    <div class="lg:hidden flex items-center gap-1.5 sm:gap-2 min-w-0">
      <x-company-logo :show-text="false" size="sm" />
      <span class="font-extrabold text-xs text-slate-800 dark:text-white truncate max-w-[110px] sm:max-w-none">
        Digital Attendance
      </span>
    </div>

    <!-- Desktop Rounded Pill Search Bar -->
    <div class="hidden sm:block relative w-full max-w-xs sm:max-w-sm md:max-w-md">
      <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
      </div>
      <input type="text"
        placeholder="Search..."
        class="w-full pl-10 pr-4 py-2 text-sm bg-white dark:bg-[#161F30] border border-slate-200/90 dark:border-slate-700/90 rounded-full text-slate-700 dark:text-slate-200 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200" />
    </div>
  </div>

  <!-- Right Side: Dark Mode, Notifications, Quick Info, User Profile -->
  <div class="flex items-center gap-1.5 sm:gap-3.5 shrink-0">
    <!-- Theme Toggle -->
    <div>
      <x-theme-toggle />
    </div>

    <!-- Notification Button with Blue Dot Badge -->
    <div class="relative">
      <button type="button"
        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white dark:bg-[#161F30] border border-slate-200/80 dark:border-slate-700/80 shadow-sm flex items-center justify-center text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 transition"
        title="Notifications">
        <x-heroicon-o-bell class="w-4 h-4 sm:w-5 sm:h-5" />
        <!-- Notification indicator badge -->
        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-indigo-600 ring-2 ring-white dark:ring-[#161F30]"></span>
      </button>
    </div>

    <!-- Quick Info Widget (matches "Your Balance $5,456" in reference image) -->
    <div class="hidden md:flex flex-col text-right pr-1">
      <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">
        Hari Ini
      </span>
      <span class="text-xs font-extrabold text-indigo-600 dark:text-indigo-400">
        {{ date('d M Y') }}
      </span>
    </div>

    <!-- User Profile Dropdown matching reference image: Avatar + "Hi, Lay" -->
    <div class="relative">
      <x-dropdown align="right" width="48">
        <x-slot name="trigger">
          <button type="button" class="flex items-center gap-2 p-1 sm:pl-1.5 sm:pr-3 sm:py-1 rounded-full bg-white dark:bg-[#161F30] border border-slate-200/80 dark:border-slate-700/80 shadow-sm hover:border-slate-300 dark:hover:border-slate-600 transition">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && $user?->profile_photo_url)
              <img class="h-7 w-7 sm:h-8 sm:w-8 rounded-full object-cover ring-2 ring-indigo-500/20"
                src="{{ $user->profile_photo_url }}"
                alt="{{ $user->name }}" />
            @else
              <div class="h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center ring-2 ring-indigo-500/20">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
              </div>
            @endif

            <div class="hidden sm:flex items-center gap-1.5 text-left">
              <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                Hi, {{ $firstName }}
              </span>
              <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-slate-400" />
            </div>
          </button>
        </x-slot>

        <x-slot name="content">
          <!-- Account Management -->
          <div class="block px-4 py-2 text-xs text-slate-400 font-semibold uppercase tracking-wider">
            {{ __('Manage Account') }}
          </div>

          <x-dropdown-link href="{{ route('profile.show') }}">
            {{ __('Profile') }}
          </x-dropdown-link>

          @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
            <x-dropdown-link href="{{ route('api-tokens.index') }}">
              {{ __('API Tokens') }}
            </x-dropdown-link>
          @endif

          <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>

          <!-- Authentication -->
          <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-rose-600 hover:text-rose-700">
              {{ __('Log Out') }}
            </x-dropdown-link>
          </form>
        </x-slot>
      </x-dropdown>
    </div>
  </div>
</header>
