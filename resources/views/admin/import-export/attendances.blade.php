<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
      Import & Export Absensi
    </h2>
  </x-slot>

  <div class="w-full">
    @livewire('admin.import-export.attendance')
  </div>
</x-app-layout>
