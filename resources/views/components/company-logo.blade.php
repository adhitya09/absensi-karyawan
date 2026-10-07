@props(['size' => 'md', 'showText' => true])

@php
  $sizes = [
    'sm' => 'w-9 h-9',
    'md' => 'w-10 h-10',
    'lg' => 'w-12 h-12',
    'xl' => 'w-14 h-14',
  ];
  $boxClass = $sizes[$size] ?? 'w-10 h-10';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
  <!-- SDS Corporate Emblem Mark -->
  <div class="{{ $boxClass }} rounded-2xl bg-gradient-to-tr from-sky-600 via-indigo-600 to-indigo-700 flex items-center justify-center shadow-md shadow-indigo-500/25 shrink-0 relative overflow-hidden group">
    <!-- Subtle Glass Specular Reflection -->
    <div class="absolute inset-0 bg-gradient-to-b from-white/25 via-transparent to-transparent pointer-events-none"></div>
    
    <!-- Vector Graphic: Interlocking S & D Duct Geometry + Attendance Pulse -->
    <svg class="w-3/5 h-3/5 text-white drop-shadow-sm" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="sdsGradTop" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#38BDF8"/>
          <stop offset="100%" stop-color="#FFFFFF"/>
        </linearGradient>
        <linearGradient id="sdsGradBottom" x1="100%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#FFFFFF"/>
          <stop offset="100%" stop-color="#A5B4FC"/>
        </linearGradient>
      </defs>
      
      <!-- Upper Duct Flow / Curve (S upper) -->
      <path d="M12 13.5C12 10.4624 14.4624 8 17.5 8H25.5C27.9853 8 30 10.0147 30 12.5C30 14.9853 27.9853 17 25.5 17H18.5C16.0147 17 14 19.0147 14 21.5" 
        stroke="url(#sdsGradTop)" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
      
      <!-- Lower Duct Flow / Curve (D lower) -->
      <path d="M26 19C26 21.4853 23.9853 23.5 21.5 23.5H14.5C12.0147 23.5 10 25.5147 10 28C10 30.4853 12.0147 32.5 14.5 32.5H22.5C26.6421 32.5 30 29.1421 30 25V18" 
        stroke="url(#sdsGradBottom)" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
      
      <!-- Digital Attendance Pulse Point -->
      <circle cx="20" cy="20.2" r="2.4" fill="#38BDF8"/>
    </svg>
  </div>

  @if ($showText)
    <div class="flex flex-col min-w-0 text-left">
      <span class="text-sm sm:text-base font-black tracking-tight text-slate-800 dark:text-white leading-tight truncate">
        Digital Attendance
      </span>
      <span class="text-[10px] font-bold tracking-tight text-indigo-600 dark:text-indigo-400 truncate mt-0.5">
        By PT Son Duct Sejahtera
      </span>
    </div>
  @endif
</div>
