<div class="w-full space-y-5">
  @php
    use Illuminate\Support\Carbon;
  @endphp
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      let currentMap = document.getElementById('currentMap');
      let map = document.getElementById('map');

      setTimeout(() => {
        toggleMap();
        toggleCurrentMap();
      }, 1000);

      function toggleCurrentMap() {
        const mapIsVisible = currentMap.style.display === "none";
        currentMap.style.display = mapIsVisible ? "block" : "none";
        document.querySelector('#toggleCurrentMap').innerHTML = mapIsVisible ?
          `<x-heroicon-s-chevron-up class="h-4 w-4" />` :
          `<x-heroicon-s-chevron-down class="h-4 w-4" />`;
      }

      function toggleMap() {
        const mapIsVisible = map.style.display === "none";
        map.style.display = mapIsVisible ? "block" : "none";
      }
    </script>
  @endpushOnce

  @if (!$isAbsence)
    <script src="{{ url('/assets/js/html5-qrcode.min.js') }}"></script>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
    @if (!$isAbsence)
      <!-- Left Column: Shift Selector & Camera Scanner (5 Cols on Desktop) -->
      <div class="lg:col-span-5 space-y-4">
        <!-- Shift Selector Card -->
        <div class="flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
          <label for="shift" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
            Pilih Jadwal Shift
          </label>
          <x-select id="shift" class="w-full text-sm" wire:model="shift_id" disabled="{{ !is_null($attendance) }}">
            <option value="">{{ __('Select Shift') }}</option>
            @foreach ($shifts as $shift)
              <option value="{{ $shift->id }}" {{ $shift->id == $shift_id ? 'selected' : '' }}>
                {{ $shift->name . ' (' . $shift->start_time . ' - ' . $shift->end_time . ')' }}
              </option>
            @endforeach
          </x-select>
          @error('shift_id')
            <x-input-error for="shift" class="mt-2" message={{ $message }} />
          @enderror
        </div>

        <!-- Scanner Viewfinder Card -->
        <div class="flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft flex flex-col items-center">
          <div class="w-full flex items-center justify-between mb-3 pb-2 border-b border-slate-100 dark:border-slate-800">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
              <x-heroicon-o-camera class="w-4 h-4 text-indigo-500" />
              Kamera Pemindai QR
            </span>
            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full">
              Realtime
            </span>
          </div>

          <div class="w-full flex justify-center p-2 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 overflow-hidden" wire:ignore>
            <div id="scanner" class="w-full max-w-[340px] sm:max-w-md min-h-[280px] sm:min-h-[340px] rounded-xl overflow-hidden">
            </div>
          </div>
          <p class="text-[11px] text-slate-400 text-center mt-2 font-medium">Arahkan kamera ke QR Code yang disediakan kantor</p>
        </div>
      </div>
    @endif

    <!-- Right Column: Status Cards & Location (7 Cols on Desktop) -->
    <div class="{{ $isAbsence ? 'lg:col-span-12' : 'lg:col-span-7' }} space-y-4">
      
      <!-- Notifications / Status Messages -->
      <div id="scanner-error" class="text-sm font-semibold text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 p-3 rounded-2xl border border-rose-200 dark:border-rose-900 empty:hidden" wire:ignore></div>
      <div id="scanner-result" class="hidden text-sm font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-2xl border border-emerald-200 dark:border-emerald-900">
        {{ $successMsg }}
      </div>

      <!-- Live Attendance KPI Status Cards -->
      <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <!-- Masuk Card -->
        @php
          $isLate = $attendance?->status == 'late';
        @endphp
        <div class="flex-card p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border {{ $isLate ? 'bg-amber-50/70 border-amber-200/80 dark:bg-amber-950/30 dark:border-amber-900/60' : 'bg-emerald-50/70 border-emerald-200/80 dark:bg-emerald-950/30 dark:border-emerald-900/60' }} shadow-soft">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider {{ $isLate ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400' }}">
              Absen Masuk
            </span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl {{ $isLate ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center shrink-0">
              <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
            </div>
          </div>
          <div class="text-lg sm:text-2xl font-black text-slate-800 dark:text-white">
            @if ($isAbsence)
              {{ __($attendance?->status) ?? '-' }}
            @else
              {{ $attendance?->time_in ? Carbon::parse($attendance?->time_in)->format('H:i:s') : 'Belum Absen' }}
            @endif
          </div>
          @if ($isLate)
            <span class="inline-block mt-1 text-[10px] sm:text-xs font-semibold text-amber-600 dark:text-amber-400">
              Terlambat
            </span>
          @endif
        </div>

        <!-- Keluar Card -->
        <div class="flex-card p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border bg-indigo-50/70 border-indigo-200/80 dark:bg-indigo-950/30 dark:border-indigo-900/60 shadow-soft">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400">
              Absen Keluar
            </span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
              <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4" />
            </div>
          </div>
          <div class="text-lg sm:text-2xl font-black text-slate-800 dark:text-white">
            @if ($isAbsence)
              {{ __($attendance?->status) ?? '-' }}
            @else
              {{ $attendance?->time_out ? Carbon::parse($attendance?->time_out)->format('H:i:s') : 'Belum Absen' }}
            @endif
          </div>
          <span class="inline-block mt-1 text-[10px] sm:text-xs font-semibold text-slate-400">
            Selesai Kerja
          </span>
        </div>
      </div>

      <!-- Location Info & Map Card -->
      <div class="flex-card p-4 sm:p-5 bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
              <x-heroicon-o-map-pin class="w-4 h-4" />
            </div>
            <div>
              <span class="text-xs font-bold text-slate-800 dark:text-white block">Posisi GPS Anda</span>
              <span class="text-[10px] text-slate-400">{{ __('Date') . ': ' . now()->format('d/m/Y') }}</span>
            </div>
          </div>

          @if (!is_null($currentLiveCoords))
            <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition" onclick="toggleCurrentMap()" id="toggleCurrentMap">
              <span class="text-[10px]">Peta</span>
              <x-heroicon-s-chevron-down class="h-3.5 w-3.5" />
            </button>
          @endif
        </div>

        @if (!is_null($currentLiveCoords))
          <div class="text-xs text-slate-600 dark:text-slate-300 mb-2 truncate">
            <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}" target="_blank"
              class="underline hover:text-indigo-500 font-mono text-[11px] sm:text-xs">
              {{ $currentLiveCoords[0] }}, {{ $currentLiveCoords[1] }}
            </a>
          </div>
        @else
          <div class="text-xs text-slate-400 mb-2">Mendeteksi koordinat GPS...</div>
        @endif

        <div class="h-48 sm:h-64 w-full rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700" id="currentMap" wire:ignore></div>
      </div>

      <!-- Quick Action Shortcuts -->
      <div class="grid grid-cols-2 gap-3" wire:ignore>
        <a href="{{ route('apply-leave') }}" class="group">
          <div class="flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-amber-500 hover:bg-amber-600 active:scale-[0.98] text-white shadow-md shadow-amber-500/20 transition-all duration-200">
            <div>
              <span class="block text-xs sm:text-sm font-bold">Ajukan Izin</span>
              <span class="text-[10px] text-amber-100">Sakit / Keperluan</span>
            </div>
            <x-heroicon-o-envelope-open class="h-5 w-5 sm:h-6 sm:w-6 text-white shrink-0 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </a>
        <a href="{{ route('attendance-history') }}" class="group">
          <div class="flex items-center justify-between p-3.5 sm:p-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white shadow-md shadow-indigo-600/20 transition-all duration-200">
            <div>
              <span class="block text-xs sm:text-sm font-bold">Riwayat Absen</span>
              <span class="text-[10px] text-indigo-200">Kalender Bulanan</span>
            </div>
            <x-heroicon-o-clock class="h-5 w-5 sm:h-6 sm:w-6 text-white shrink-0 group-hover:translate-x-0.5 transition-transform" />
          </div>
        </a>
      </div>

      <!-- Attendance Point Map Toggle (if attended) -->
      @if (!is_null($attendance?->lat_lng))
        <div class="flex-card p-4 rounded-2xl bg-white dark:bg-[#161F30] border border-slate-100 dark:border-slate-800">
          <button class="w-full flex items-center justify-between text-left text-xs font-bold text-slate-700 dark:text-slate-300" onclick="toggleMap()" id="toggleMap">
            <span class="flex items-center gap-2">
              <x-heroicon-o-check-badge class="w-4 h-4 text-emerald-500" />
              Titik Lokasi Absen Tercatat
            </span>
            <span class="font-mono text-[11px] text-indigo-500">{{ $attendance?->latitude }}, {{ $attendance?->longitude }}</span>
          </button>
          <div class="my-3 h-48 w-full rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700" id="map" wire:ignore></div>
        </div>
      @else
        <div class="hidden" id="map" wire:ignore></div>
      @endif

    </div>
  </div>
</div>

@script
  <script>
    const errorMsg = document.querySelector('#scanner-error');
    getLocation();

    async function getLocation() {
      if (navigator.geolocation) {
        const map = L.map('currentMap');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 21,
        }).addTo(map);
        navigator.geolocation.watchPosition((position) => {
          console.log(position);
          $wire.$set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
          map.setView([
            Number(position.coords.latitude),
            Number(position.coords.longitude),
          ], 13);
          L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
        }, (err) => {
          console.error(`ERROR(${err.code}): ${err.message}`);
          alert('{{ __('Please enable your location') }}');
        });
      } else {
        document.querySelector('#scanner-error').innerHTML = "Gagal mendeteksi lokasi";
      }
    }

    if (!$wire.isAbsence) {
      const scanner = new Html5Qrcode('scanner');

      const config = {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
        fps: 15,
        aspectRatio: 1,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
          const edge = Math.min(viewfinderWidth, viewfinderHeight);
          const qrboxEdge = Math.max(160, Math.floor(edge * 0.72));
          return { width: qrboxEdge, height: qrboxEdge };
        },
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
      };

      async function startScanning() {
        if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
          return scanner.resume();
        }
        await scanner.start({
            facingMode: "environment"
          },
          config,
          onScanSuccess,
        );
      }

      async function onScanSuccess(decodedText, decodedResult) {
        console.log(`Code matched = ${decodedText}`, decodedResult);

        if (scanner.getState() === Html5QrcodeScannerState.SCANNING) {
          scanner.pause(true);
        }

        if (!(await checkTime())) {
          await startScanning();
          return;
        }

        const result = await $wire.scan(decodedText);

        if (result === true) {
          return onAttendanceSuccess();
        } else if (typeof result === 'string') {
          errorMsg.innerHTML = result;
        }

        setTimeout(async () => {
          await startScanning();
        }, 500);
      }

      async function checkTime() {
        const attendance = await $wire.getAttendance();

        if (attendance) {
          const timeIn = new Date(attendance.time_in).valueOf();
          const diff = (Date.now() - timeIn) / (1000 * 3600);
          const minAttendanceTime = 1;
          console.log(`Difference = ${diff}`);
          if (diff <= minAttendanceTime) {
            const timeIn = new Date(attendance.time_in).toLocaleTimeString([], {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: false,
            });
            const confirmation = confirm(
              `Anda baru saja absen pada ${timeIn}, apakah ingin melanjutkan untuk absen keluar?`
            );
            return confirmation;
          }
        }
        return true;
      }

      function onAttendanceSuccess() {
        scanner.stop();
        errorMsg.innerHTML = '';
        document.querySelector('#scanner-result').classList.remove('hidden');
      }

      const observer = new MutationObserver((mutationList, observer) => {
        const classes = ['text-white', 'bg-blue-500', 'dark:bg-blue-400', 'rounded-md', 'px-3', 'py-1'];
        for (const mutation of mutationList) {
          if (mutation.type === 'childList') {
            const startBtn = document.querySelector('#html5-qrcode-button-camera-start');
            const stopBtn = document.querySelector('#html5-qrcode-button-camera-stop');
            const fileBtn = document.querySelector('#html5-qrcode-button-file-selection');
            const permissionBtn = document.querySelector('#html5-qrcode-button-camera-permission');

            if (startBtn) {
              startBtn.classList.add(...classes);
              stopBtn.classList.add(...classes, 'bg-red-500');
              fileBtn.classList.add(...classes);
            }

            if (permissionBtn)
              permissionBtn.classList.add(...classes);
          }
        }
      });

      observer.observe(document.querySelector('#scanner'), {
        childList: true,
        subtree: true,
      });

      const shift = document.querySelector('#shift');
      const msg = 'Pilih shift terlebih dahulu';
      let isRendered = false;
      setTimeout(() => {
        if (!shift.value) {
          errorMsg.innerHTML = msg;
        } else {
          startScanning();
          isRendered = true;
        }
      }, 1000);
      shift.addEventListener('change', () => {
        if (!isRendered) {
          startScanning();
          isRendered = true;
          errorMsg.innerHTML = '';
        }
        if (!shift.value) {
          scanner.pause(true);
          errorMsg.innerHTML = msg;
        } else if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
          scanner.resume();
          errorMsg.innerHTML = '';
        }
      });

      const map = L.map('map').setView([
        Number({{ $attendance?->latitude }}),
        Number({{ $attendance?->longitude }}),
      ], 13);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 21,
      }).addTo(map);
      L.marker([
        Number({{ $attendance?->latitude }}),
        Number({{ $attendance?->longitude }}),
      ]).addTo(map);
    }
  </script>
@endscript
