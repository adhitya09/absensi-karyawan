<div class="flex min-h-screen flex-col items-center justify-center bg-[#F4F6FC] px-3.5 py-6 sm:px-6 dark:bg-[#0B0F19]">
  <div class="mb-4 text-center">
    {{ $logo }}
  </div>

  <div class="w-full overflow-hidden bg-white px-5 py-6 sm:px-8 sm:py-8 shadow-soft dark:bg-[#161F30] border border-slate-100/90 dark:border-slate-800/80 sm:max-w-md rounded-2xl sm:rounded-3xl">
    {{ $slot }}
  </div>
</div>
