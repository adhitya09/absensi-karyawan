<div class="space-y-5">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">
        Data Divisi
      </h3>
      <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola data departemen dan divisi karyawan</p>
    </div>
    <x-button wire:click="showCreating" class="justify-center w-full sm:w-auto">
      <x-heroicon-o-plus class="mr-2 h-4 w-4" /> Tambah Divisi
    </x-button>
  </div>
  <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-800/80">
    <table class="w-full divide-y divide-slate-100 dark:divide-slate-800">
      <thead class="bg-slate-50/80 dark:bg-slate-800/60">
      <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          Divisi
        </th>
        <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Actions</span>
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @foreach ($divisions as $division)
        <tr>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $division->name }}
          </td>
          <td class="relative flex justify-end gap-2 px-6 py-4">
            <x-button wire:click="edit({{ $division->id }})">
              Edit
            </x-button>
            <x-danger-button wire:click="confirmDeletion({{ $division->id }}, '{{ $division->name }}')">
              Delete
            </x-danger-button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  </div>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">
      Hapus Divisi
    </x-slot>

    <x-slot name="content">
      Apakah Anda yakin ingin menghapus <b>{{ $deleteName }}</b>?
    </x-slot>

    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('confirmingDeletion')" wire:loading.attr="disabled">
        {{ __('Cancel') }}
      </x-secondary-button>

      <x-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
        {{ __('Confirm') }}
      </x-danger-button>
    </x-slot>
  </x-confirmation-modal>

  <x-dialog-modal wire:model="creating">
    <x-slot name="title">
      Divisi Baru
    </x-slot>

    <form wire:submit="create">
      <x-slot name="content">
        <x-label for="name">Nama Divisi</x-label>
        <x-input id="name" class="mt-1 block w-full" type="text" wire:model="name" />
        @error('name')
          <x-input-error for="name" class="mt-2" message="{{ $message }}" />
        @enderror
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('creating')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
          {{ __('Confirm') }}
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>

  <x-dialog-modal wire:model="editing">
    <x-slot name="title">
      Edit Divisi
    </x-slot>

    <form wire:submit.prevent="update">
      <x-slot name="content">
        <x-label for="name">Nama Divisi</x-label>
        <x-input id="name" class="mt-1 block w-full" type="text" wire:model="name" />
        @error('name')
          <x-input-error for="name" class="mt-2" message="{{ $message }}" />
        @enderror
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
          {{ __('Confirm') }}
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>
</div>
