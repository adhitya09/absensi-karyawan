<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
      {{ __('Riwayat Absensi') }}
    </h2>
  </x-slot>

  <div class="w-full">
    @livewire('attendance-history-component')
  </div>
</x-app-layout>
