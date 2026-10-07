<div class="space-y-5">
  <script src="{{ url('/assets/js/qrcode.min.js') }}"></script>
  
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">
        Kelola QR Code Absensi
      </h3>
      <p class="text-xs text-slate-400 font-medium mt-0.5">Generate QR Code lokasi kantor untuk dipindai karyawan</p>
    </div>
    <div class="flex items-center gap-2">
      <x-button href="{{ route('admin.barcodes.create') }}" class="justify-center flex-1 sm:flex-initial">
        <x-heroicon-o-plus class="w-4 h-4 mr-1.5" /> Buat Baru
      </x-button>
      <x-secondary-button href="{{ route('admin.barcodes.downloadall') }}" class="justify-center flex-1 sm:flex-initial">
        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1.5" /> Unduh Semua
      </x-secondary-button>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
    @foreach ($barcodes as $barcode)
      <div
        class="flex flex-col rounded-2xl sm:rounded-3xl bg-white dark:bg-[#161F30] p-4 sm:p-5 border border-slate-100/90 dark:border-slate-800/80 shadow-soft hover:shadow-soft-lg transition-all duration-200">

        <div class="w-full flex items-center justify-center p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm mb-3 overflow-hidden">
          <div id="qrcode{{ $barcode->id }}" class="flex items-center justify-center max-w-full p-1 bg-white">
          </div>
        </div>

        <h3 class="mb-1 text-center text-base sm:text-lg font-bold leading-tight text-slate-800 dark:text-white">
          {{ $barcode->name }}
        </h3>

        <div class="space-y-1 text-xs text-slate-500 dark:text-slate-400 mb-4 text-center">
          <div>
            <a href="https://www.google.com/maps/search/?api=1&query={{ $barcode->latitude }},{{ $barcode->longitude }}"
              target="_blank" class="hover:text-indigo-600 font-mono text-[11px] underline">
              {{ $barcode->latitude }}, {{ $barcode->longitude }}
            </a>
          </div>
          <div class="text-[11px]">Radius: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $barcode->radius }} meter</span></div>
        </div>

        <div class="mt-auto flex items-center justify-center gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <x-secondary-button href="{{ route('admin.barcodes.download', $barcode->id) }}" class="text-xs py-1.5 px-3">
            Unduh
          </x-secondary-button>
          <x-button href="{{ route('admin.barcodes.edit', $barcode->id) }}" class="text-xs py-1.5 px-3">
            Edit
          </x-button>
          <x-danger-button wire:click="confirmDeletion({{ $barcode->id }}, '{{ $barcode->name }}')" class="text-xs py-1.5 px-3">
            Hapus
          </x-danger-button>
        </div>
      </div>
    @endforeach
  </div>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">
      Hapus Barcode
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
</div>

@script
  <script type="text/javascript">
    let barcodes = @json(
      $barcodes->map(fn($barcode) => [
          'id' => $barcode->id,
          'value' => $barcode->value
      ])
    );

    barcodes.forEach(({ id, value }) => {
      new QRCode(document.getElementById("qrcode" + id), {
        text: value,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
      });
    });
    setInterval(() => {
      if (isDark == $store.darkMode.on &&
        document.getElementById("qrcode" + barcodes[0]['id']).hasAttribute("title")) {
        return;
      }
      isDark = $store.darkMode.on;
      barcodes.forEach(({ id, value }) => {
        if (!document.getElementById("qrcode" + id)) {
          return;
        }
        document.getElementById("qrcode" + id).innerHTML = "";
        new QRCode(document.getElementById("qrcode" + id), {
          text: value,
          colorDark: $store.darkMode.on ? "#ffffff" : "#000000",
          colorLight: $store.darkMode.on ? "#000000" : "#ffffff",
          correctLevel: QRCode.CorrectLevel.M,
        });
      });
    }, 250);
  </script>
@endscript
