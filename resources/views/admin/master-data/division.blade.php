<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100">
      {{ __('Division') }}
    </h2>
  </x-slot>

  <div class="flex-card bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft p-4 sm:p-6 lg:p-8">
    @livewire('admin.master-data.division-component')
  </div>
</x-app-layout>
