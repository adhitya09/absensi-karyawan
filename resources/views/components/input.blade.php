@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'border-slate-200/90 dark:border-slate-700/80 bg-white dark:bg-[#161F30] text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-2xl shadow-sm text-sm dark:[color-scheme:dark] transition duration-150',
]) !!}>
