<x-modal wire:model="showDetail" onclose="removeMap()">
  <div class="px-6 py-4">
    @if ($currentAttendance)
      @php
        $isExcused = $currentAttendance['status'] == 'excused' || $currentAttendance['status'] == 'sick';
        $showMap = $currentAttendance['latitude'] && $currentAttendance['longitude'] && !$isExcused;
      @endphp
      <h3 class="mb-3 text-xl font-semibold dark:text-white">{{ $currentAttendance['name'] }}</h3>
      <div class="mb-3 w-full">
        <x-label for="nip" value="{{ __('NIP') }}"></x-label>
        <x-input type="text" class="w-full" id="nip" disabled value="{{ $currentAttendance['nip'] }}"></x-input>
      </div>
      <div class="mb-3 flex w-full gap-3">
        <div class="w-full">
          <x-label for="date" value="{{ __('Date') }}"></x-label>
          <x-input type="text" class="w-full" id="date" disabled
            value="{{ $currentAttendance['date'] }}"></x-input>
        </div>
        <div class="w-full">
          <x-label for="status" value="{{ __('Status') }}"></x-label>
          <x-input type="text" class="w-full" id="status" disabled
            value="{{ __($currentAttendance['status']) }}"></x-input>
        </div>
      </div>
      @if ($isExcused)
        <div class="mb-3 w-full">
          <x-label for="address" value="{{ __('Address') }}" />
          <x-input type="text" class="w-full" id="address" disabled value="{{ $currentAttendance['address'] }}" />
        </div>
      @endif
      <div class="flex flex-col gap-3">
        @if ($currentAttendance['attachment'])
          <x-label for="attachment" value="{{ __('Attachment') }}"></x-label>
          <img src="{{ $currentAttendance['attachment'] }}" alt="Attachment"
            class="max-h-48 object-contain sm:max-h-64 md:max-h-72">
        @endif
        @if ($currentAttendance['note'])
          <x-label for="note" value="Keterangan" />
          <x-textarea type="text" id="note" disabled value="{{ $currentAttendance['note'] }}" />
        @endif
        @if ($showMap)
          <x-label for="map" value="Koordinat Lokasi Absen"></x-label>
          <p class="dark:text-gray-300">
            {{ $currentAttendance['latitude'] }}, {{ $currentAttendance['longitude'] }}
          </p>
          <div class="my-2 h-52 w-full md:h-64" id="map"></div>
        @endif
        @if ($currentAttendance['time_in'] || $currentAttendance['time_out'])
          <div class="grid grid-cols-2 gap-3">
            <div>
              <x-label for="time_in" value="Waktu Masuk"></x-label>
              <x-input type="text" id="time_in" class="w-full mt-1" disabled
                value="{{ $currentAttendance['time_in'] ?? '-' }}"></x-input>
            </div>
            <div>
              <x-label for="time_out" value="Waktu Keluar"></x-label>
              <x-input type="text" id="time_out" class="w-full mt-1" disabled
                value="{{ $currentAttendance['time_out'] ?? '-' }}"></x-input>
            </div>
          </div>
        @endif

        @if (!empty($currentAttendance['checkout_status_label']))
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 space-y-1.5 text-xs">
            <div class="flex items-center justify-between">
              <span class="text-slate-500 font-medium">Status Kepulangan:</span>
              <span class="font-bold px-2.5 py-0.5 rounded-full {{ $currentAttendance['checkout_badge_class'] ?? 'bg-slate-100 text-slate-600' }}">
                {{ $currentAttendance['checkout_status_label'] }} {{ $currentAttendance['checkout_diff_formatted'] ? '(' . $currentAttendance['checkout_diff_formatted'] . ')' : '' }}
              </span>
            </div>
            @if (!empty($currentAttendance['checkout_note']))
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                {{ $currentAttendance['checkout_note'] }}
              </p>
            @endif
          </div>
        @endif

        <div class="flex gap-3">
          @if ($currentAttendance['shift'] ?? false)
            <div class="w-full">
              <x-label for="shift" value="Shift"></x-label>
              <x-input class="w-full" type="text" id="shift" disabled
                value="{{ $currentAttendance['shift']['name'] }}"></x-input>
            </div>
          @endif
          @if ($currentAttendance['barcode'] ?? false)
            <div class="w-full">
              <x-label for="barcode" value="Barcode"></x-label>
              <x-input class="w-full" type="text" id="barcode" disabled
                value="{{ $currentAttendance['barcode']['name'] }}"></x-input>
            </div>
          @endif
        </div>
      </div>
    @endif
  </div>
</x-modal>

@push('attendance-detail-scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    let map = null;

    function setLocation(lat, lng) {
      removeMap();
      setTimeout(() => {
        map = L.map('map').setView([Number(lat), Number(lng)], 19);
        L.marker([Number(lat), Number(lng)]).addTo(map);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 21,
        }).addTo(map);
      }, 500);
    }

    function removeMap() {
      if (map !== null) map.remove();
      map = null;
    }
  </script>
@endpush
