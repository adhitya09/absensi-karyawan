<x-app-layout>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce

  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('New Barcode') }}
    </h2>
  </x-slot>

  <div class="flex-card bg-white dark:bg-[#161F30] rounded-2xl sm:rounded-3xl border border-slate-100/90 dark:border-slate-800/80 shadow-soft p-4 sm:p-6 lg:p-8">
    <div class="mb-5">
      <x-secondary-button href="{{ route('admin.barcodes') }}">
        <x-heroicon-o-chevron-left class="mr-1.5 h-4 w-4" />
        Kembali
      </x-secondary-button>
    </div>
          <form action="{{ route('admin.barcodes.store') }}" method="post">
            @csrf

            <div class="flex flex-col gap-4 md:flex-row md:items-start md:gap-3">
              <div class="w-full">
                <x-label for="name">Nama Barcode</x-label>
                <x-input name="name" id="name" class="mt-1 block w-full" type="text" :value="old('name')"
                  placeholder="Barcode Baru" />
                @error('name')
                  <x-input-error for="name" class="mt-2" message="{{ $message }}" />
                @enderror
              </div>
              <div class="w-full">
                <x-label for="value">Value Barcode</x-label>
                @livewire('admin.barcode-value-input-component')
              </div>
            </div>

            <div class="mt-4 flex gap-3">
              <div class="w-full">
                <x-label for="radius">Radius Valid Absen</x-label>
                <x-input name="radius" id="radius" class="mt-1 block w-full" type="number" :value="old('radius') ?? 50"
                  placeholder="50 (meter)" />
                <p class="text-[11px] text-slate-400 mt-1">Masukkan jarak maksimal (meter). Isi <b>0</b> untuk bebas radius (bisa absen dari mana saja).</p>
                @error('radius')
                  <x-input-error for="radius" class="mt-2" message="{{ $message }}" />
                @enderror
              </div>
              <div class="w-full">
              </div>
            </div>

            <div class="mt-5">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-gray-200">{{ __('Coordinate') }}</h3>
                <span class="text-xs text-slate-400">Geser pin di peta atau masukkan koordinat</span>
              </div>

              <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="w-full">
                  <x-label for="lat">Latitude</x-label>
                  <x-input name="lat" id="lat" class="mt-1 block w-full" type="text" :value="old('lat')"
                    placeholder="cth: -6.12345" />
                  @error('lat')
                    <x-input-error for="lat" class="mt-2" message="{{ $message }}" />
                  @enderror
                </div>
                <div class="w-full">
                  <x-label for="lng">Longitude</x-label>
                  <x-input name="lng" id="lng" class="mt-1 block w-full" type="text" :value="old('lng')"
                    placeholder="cth: 106.81234" />
                  @error('lng')
                    <x-input-error for="lng" class="mt-2" message="{{ $message }}" />
                  @enderror
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-2.5 mt-4">
                <x-secondary-button type="button" id="btn-get-gps" onclick="getCurrentGpsLocation()" class="text-nowrap">
                  <x-heroicon-s-map-pin class="mr-1.5 h-4 w-4 text-emerald-500" />
                  <span id="btn-gps-text">Ambil Lokasi Saya Sekarang (GPS)</span>
                </x-secondary-button>
                <x-button type="button" onclick="toggleMap()" class="text-nowrap">
                  <x-heroicon-s-map class="mr-1.5 h-4 w-4" />
                  <span>Tampilkan/Sembunyikan Peta</span>
                </x-button>
              </div>

              <div id="map" class="my-4 h-72 w-full md:h-96 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-inner"></div>

              <div class="mt-6 flex items-center justify-end">
                <x-button class="w-full sm:w-auto justify-center">
                  {{ __('Save') }}
                </x-button>
              </div>
            </div>
          </form>
  </div>

  @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
      integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      let barcodeMap = null;
      let barcodeMarker = null;

      function setupBarcodeMap(initialLat, initialLng) {
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const mapContainer = document.getElementById('map');
        if (!mapContainer) return;

        let lat = parseFloat(initialLat);
        let lng = parseFloat(initialLng);
        if (isNaN(lat) || isNaN(lng)) {
          lat = -6.200000;
          lng = 106.816666;
        }

        if (barcodeMap) {
          barcodeMap.remove();
        }

        barcodeMap = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; OpenStreetMap'
        }).addTo(barcodeMap);

        barcodeMarker = L.marker([lat, lng], { draggable: true }).addTo(barcodeMap);

        // Marker drag updates input
        barcodeMarker.on('dragend', function() {
          const pos = barcodeMarker.getLatLng();
          latInput.value = pos.lat.toFixed(6);
          lngInput.value = pos.lng.toFixed(6);
        });

        // Click on map moves marker & updates input
        barcodeMap.on('click', function(e) {
          barcodeMarker.setLatLng(e.latlng);
          latInput.value = e.latlng.lat.toFixed(6);
          lngInput.value = e.latlng.lng.toFixed(6);
        });

        // Smart input handler: automatically parses comma-separated coords (e.g. from Google Maps paste)
        function handleInput(e) {
          const val = e.target.value.trim();
          if (val.includes(',') || (val.includes(' ') && val.split(/\s+/).length === 2)) {
            const parts = val.split(/[,\s]+/).map(p => parseFloat(p.trim())).filter(p => !isNaN(p));
            if (parts.length >= 2) {
              latInput.value = parts[0].toFixed(6);
              lngInput.value = parts[1].toFixed(6);
            }
          }
          syncMapFromInput();
        }

        // Input change immediately moves map & marker!
        function syncMapFromInput() {
          const newLat = parseFloat(latInput.value);
          const newLng = parseFloat(lngInput.value);
          if (!isNaN(newLat) && !isNaN(newLng)) {
            if (barcodeMarker) barcodeMarker.setLatLng([newLat, newLng]);
            if (barcodeMap) barcodeMap.setView([newLat, newLng], barcodeMap.getZoom() || 15);
          }
        }

        latInput.addEventListener('input', handleInput);
        lngInput.addEventListener('input', handleInput);
        latInput.addEventListener('change', syncMapFromInput);
        lngInput.addEventListener('change', syncMapFromInput);

        // Initial populate if empty
        if (!latInput.value || !lngInput.value) {
          latInput.value = lat.toFixed(6);
          lngInput.value = lng.toFixed(6);
        }

        setTimeout(() => {
          if (barcodeMap) barcodeMap.invalidateSize();
        }, 300);
      }

      function getCurrentGpsLocation() {
        if (!navigator.geolocation) {
          alert("Browser Anda tidak mendukung geolokasi GPS");
          return;
        }
        const btnText = document.getElementById('btn-gps-text');
        if (btnText) btnText.innerText = "Mengambil koordinat GPS...";

        navigator.geolocation.getCurrentPosition(
          function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('lat').value = lat.toFixed(6);
            document.getElementById('lng').value = lng.toFixed(6);

            if (barcodeMap && barcodeMarker) {
              barcodeMarker.setLatLng([lat, lng]);
              barcodeMap.setView([lat, lng], 16);
              barcodeMap.invalidateSize();
            } else {
              setupBarcodeMap(lat, lng);
            }
            if (btnText) btnText.innerText = "Ambil Lokasi Saya Sekarang (GPS)";
          },
          function(err) {
            if (btnText) btnText.innerText = "Ambil Lokasi Saya Sekarang (GPS)";
            alert("Gagal membaca GPS: " + err.message + ". Pastikan izin lokasi aktif di browser.");
          },
          { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
      }
      }

      function toggleMap() {
        const mapEl = document.getElementById('map');
        const isHidden = mapEl.style.display === "none";
        mapEl.style.display = isHidden ? "block" : "none";
        if (isHidden && barcodeMap) {
          setTimeout(() => {
            barcodeMap.invalidateSize();
          }, 150);
        }
      }

      window.addEventListener("load", function() {
        const initialLat = document.getElementById('lat').value;
        const initialLng = document.getElementById('lng').value;
        setupBarcodeMap(initialLat, initialLng);
      });
    </script>
  @endPushOnce
</x-app-layout>
