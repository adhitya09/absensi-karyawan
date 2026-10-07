<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
      {{ __('Presensi Scan QR') }}
    </h2>
  </x-slot>

  <div class="w-full">
    @livewire('scan-component')
  </div>
</x-app-layout>
