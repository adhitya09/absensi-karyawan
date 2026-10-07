@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-4 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="p-4 sm:p-6 bg-white dark:bg-[#161F30] border border-slate-100/90 dark:border-slate-800/80 shadow-soft {{ isset($actions) ? 'rounded-t-2xl sm:rounded-t-3xl' : 'rounded-2xl sm:rounded-3xl' }}">
                <div class="grid grid-cols-6 gap-4 sm:gap-6">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-4 py-3 bg-slate-50/80 dark:bg-[#121826] border-x border-b border-slate-100/90 dark:border-slate-800/80 text-end sm:px-6 shadow-soft rounded-b-2xl sm:rounded-b-3xl">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
