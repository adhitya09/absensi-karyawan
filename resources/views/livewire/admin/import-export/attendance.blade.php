<div class="space-y-6">
  <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:gap-6">
    @if ($mode != 'import')
      <div class="flex-card p-4 sm:p-6 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <h3 class="mb-4 text-base sm:text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-indigo-500" />
          Ekspor Data Absensi
        </h3>
        <form wire:submit.prevent="export" class="space-y-3.5">
          <div class="flex flex-col gap-1.5">
            <x-label for="year" value="Per Tahun" class="text-xs font-semibold"></x-label>
            <x-input type="number" min="1970" max="2099" name="year" id="year" wire:model.live="year" class="w-full text-xs sm:text-sm" />
          </div>
          <div class="flex flex-col gap-1.5">
            <x-label for="month" value="Per Bulan" class="text-xs font-semibold"></x-label>
            <x-input type="month" name="month" id="month" wire:model.live="month" class="w-full text-xs sm:text-sm" />
          </div>
          <div>
            <x-label for="division" value="Divisi" class="text-xs font-semibold mb-1"></x-label>
            <x-select id="division" name="division" class="w-full text-xs sm:text-sm" wire:model.live="division">
              <option value="">{{ __('Select Division') }}</option>
              @foreach (App\Models\Division::all() as $division)
                <option value="{{ $division->id }}">
                  {{ $division->name }}
                </option>
              @endforeach
            </x-select>
          </div>
          <div>
            <x-label for="jobTitle" value="Jabatan" class="text-xs font-semibold mb-1"></x-label>
            <x-select id="jobTitle" name="job_title" class="w-full text-xs sm:text-sm" wire:model.live="job_title">
              <option value="">{{ __('Select Job Title') }}</option>
              @foreach (App\Models\JobTitle::all() as $jobTitle)
                <option value="{{ $jobTitle->id }}">
                  {{ $jobTitle->name }}
                </option>
              @endforeach
            </x-select>
          </div>
          <div>
            <x-label for="education" value="Pendidikan" class="text-xs font-semibold mb-1"></x-label>
            <x-select id="education" name="education" class="w-full text-xs sm:text-sm" wire:model.live="education">
              <option value="">{{ __('Select Education') }}</option>
              @foreach (App\Models\Education::all() as $education)
                <option value="{{ $education->id }}">
                  {{ $education->name }}
                </option>
              @endforeach
            </x-select>
          </div>
          <div class="flex flex-col sm:flex-row items-center gap-2 pt-2">
            <x-secondary-button type="button" wire:click="preview" class="w-full sm:w-1/2 justify-center">
              @if ($mode == 'export')
                {{ __('Cancel') }}
              @else
                {{ __('Preview') }}
              @endif
            </x-secondary-button>
            <x-button class="w-full sm:w-1/2 justify-center" wire:loading.attr="disabled">
              {{ __('Export') }}
            </x-button>
          </div>
        </form>
      </div>
    @endif
    @if ($mode != 'export')
      <div class="flex-card p-4 sm:p-6 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <h3 class="mb-4 text-base sm:text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-emerald-500" />
          Impor Data Absensi
        </h3>
        <form x-data="{ file: null }" wire:submit.prevent="import" method="post" enctype="multipart/form-data" class="space-y-4">
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
              x-text="file ? '{{ __('Import') }} ' + file.name : '{{ __('Import') }}'">
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
            <th class="{{ $thClass }}">Date</th>
            <th class="{{ $thClass }}">Name</th>
            <th class="{{ $thClass }}">NIP</th>
            <th class="{{ $thClass }} text-nowrap">Time In</th>
            <th class="{{ $thClass }} text-nowrap">Time Out</th>
            <th class="{{ $thClass }}">Shift</th>
            <th class="{{ $thClass }} text-nowrap">Barcode Id</th>
            <th class="{{ $thClass }}">Coordinates</th>
            <th class="{{ $thClass }}">Status</th>
            <th class="{{ $thClass }}">Note</th>
            <th class="{{ $thClass }}">Attachment</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
          @foreach ($attendances as $attendance)
            <tr class="{{ $trClass }}">
              <td class="px-2 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                {{ $loop->iteration }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">{{ $attendance->date?->format('Y-m-d') }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->user?->name }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->user?->nip }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->time_in?->format('H:i:s') }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->time_out?->format('H:i:s') }}</td>
              <td class="{{ $tdClass }} text-nowrap">{{ $attendance->shift?->name }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->barcode_id }}</td>
              <td class="{{ $tdClass }}">
                {{ $attendance->lat_lng ? $attendance->latitude . ',' . $attendance->longitude : null }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">{{ __($attendance->status) }}</td>
              <td class="{{ $tdClass }}">
                <div class="w-48">{{ Str::limit($attendance->note, 30, '...') }}</div>
              </td>
              <td class="{{ $tdClass }}">
                <img src="{{ $attendance->attachment }}" class="max-h-48 object-contain">
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
