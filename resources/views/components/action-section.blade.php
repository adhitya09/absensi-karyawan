<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-4 md:mt-0 md:col-span-2">
        <div class="p-4 sm:p-6 bg-white dark:bg-[#161F30] border border-slate-100/90 dark:border-slate-800/80 shadow-soft rounded-2xl sm:rounded-3xl">
            {{ $content }}
        </div>
    </div>
</div>
