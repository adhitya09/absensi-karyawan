<div class="space-y-6">
  <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:gap-6">
    @if ($mode != 'import')
      <div class="flex-card p-4 sm:p-6 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <h3 class="mb-4 text-base sm:text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-indigo-500" />
          Ekspor Data Karyawan / Admin
        </h3>
        <form wire:submit.prevent="export" class="space-y-4">
          <div class="space-y-2.5 p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800">
            <x-label for="user" class="flex items-center cursor-pointer">
              <x-checkbox value="user" id="user" wire:model.live="groups" />
              <span class="ms-2.5 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('Employee') }}</span>
            </x-label>
            <x-label for="admin" class="flex items-center cursor-pointer">
              <x-checkbox value="admin" id="admin" wire:model.live="groups" />
              <span class="ms-2.5 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('Admin') }}</span>
            </x-label>
            <x-label for="superadmin" class="flex items-center cursor-pointer">
              <x-checkbox value="superadmin" id="superadmin" wire:model.live="groups" />
              <span class="ms-2.5 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('Super Admin') }}</span>
            </x-label>
          </div>
          @error('groups')
            <x-input-error for="groups" class="mt-2" message="{{ $message }}" />
          @enderror
          <div class="flex flex-col sm:flex-row items-center gap-2 pt-2">
            <x-secondary-button type="button" wire:click="preview" class="w-full sm:w-1/2 justify-center">
              @if ($mode == 'export')
                {{ __('Cancel') }}
              @else
                {{ __('Preview') }}
              @endif
            </x-secondary-button>
            <x-button wire:click="export" class="w-full sm:w-1/2 justify-center">
              {{ $mode == 'export' ? __('Confirm & Export') : __('Export') }}
            </x-button>
          </div>
        </form>
      </div>
    @endif
    @if ($mode != 'export')
      <div class="flex-card p-4 sm:p-6 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <h3 class="mb-4 text-base sm:text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-emerald-500" />
          Impor Data Karyawan / Admin
        </h3>
        <form x-data="{ file: null }" method="post" wire:submit.prevent="import" enctype="multipart/form-data" class="space-y-4">
          @csrf
          <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 flex flex-col items-center justify-center text-center">
            <x-heroicon-o-document-arrow-up class="w-10 h-10 text-slate-400 mb-2" />
            <div class="flex flex-wrap items-center justify-center gap-2 mb-2">
              <x-secondary-button type="button" x-on:click.prevent="$refs.file.click()"
                x-text="file ? 'Ganti File' : 'Pilih File Excel'" class="text-xs">
                Pilih File
              </x-secondary-button>
              <x-secondary-button type="button" x-show="file"
                x-on:click.prevent="$refs.file.files[0] = null; file = null; $wire.$set('file', null)" class="text-xs">
                Hapus File
              </x-secondary-button>
            </div>
            <div class="text-xs text-slate-500 dark:text-gray-300 font-mono truncate max-w-[220px]" x-text="file ? file.name : 'File (.xlsx, .csv) belum dipilih'"></div>
            <x-input type="file" class="hidden" name="file" x-ref="file"
              x-on:change="file = $refs.file.files[0]" wire:model.live="file" />
          </div>

          <div>
            <x-button class="w-full justify-center"
              x-text="file ? '{{ __('Confirm & Import') }} ' + file.name : '{{ __('Import') }}'">
            </x-button>
          </div>
        </form>
      </div>
    @endif
  </div>
  @if ($mode && $previewing)
    <div class="sm:hidden flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500 mt-4 mb-1 font-medium">
      <span>&larr;&rarr;</span> Geser tabel pratinjau untuk kolom lainnya
    </div>
    <div class="mt-2 w-full overflow-x-auto text-sm rounded-2xl border border-slate-100 dark:border-slate-800 shadow-soft">
      @php
        $trClass = 'divide-x divide-gray-200 dark:divide-gray-700';
        $thClass = 'px-4 py-3 text-left font-semibold dark:text-white';
        $tdClass = 'px-4 py-4 text-sm font-medium text-gray-900 dark:text-white';
      @endphp
      <table class="w-full divide-y divide-gray-200 border dark:divide-gray-700 dark:border-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr class="{{ $trClass }}">
            <th scope="col" class="px-2 py-3 text-left font-semibold dark:text-white">
              No
            </th>
            <th scope="col" class="{{ $thClass }}">
              NIP
            </th>
            <th scope="col" class="{{ $thClass }}">
              Name
            </th>
            <th scope="col" class="{{ $thClass }}">
              Email
            </th>
            <th scope="col" class="{{ $thClass }}">
              Phone
            </th>
            <th scope="col" class="{{ $thClass }}">
              Gender
            </th>
            <th scope="col" class="{{ $thClass }}">
              Birth Date
            </th>
            <th scope="col" class="{{ $thClass }}">
              Birth Place
            </th>
            <th scope="col" class="{{ $thClass }}">
              Address
            </th>
            <th scope="col" class="{{ $thClass }}">
              City
            </th>
            <th scope="col" class="{{ $thClass }}">
              Education
            </th>
            <th scope="col" class="{{ $thClass }}">
              Division
            </th>
            <th scope="col" class="{{ $thClass }}">
              Job Title
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
          @foreach ($users as $user)
            <tr class="{{ $trClass }}">
              <td class="px-2 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                {{ $loop->iteration }}
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->nip }}
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->name }}
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->email }}
              </td>
              <td class="{{ $tdClass }}">
                <div class="w-32">{{ $user->phone }}</div>
              </td>
              <td class="{{ $tdClass }}">
                {{ $user->gender }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->birth_date?->format('Y-m-d') }}
              </td>
              <td class="{{ $tdClass }}">
                {{ Str::limit($user->birth_place, 20, '...') }}
              </td>
              <td class="{{ $tdClass }}">
                <div class="w-48">{{ Str::limit($user->address, 90, '...') }}</div>
              </td>
              <td class="{{ $tdClass }}">{{ $user->city }}</td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->education?->name }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->division?->name }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">
                {{ $user->jobTitle?->name }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
