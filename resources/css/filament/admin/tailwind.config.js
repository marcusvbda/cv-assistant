import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    safelist: [
        'text-green-500',
        'text-yellow-400',
        'text-red-400',
        'bg-green-500',
        'bg-yellow-400',
        'bg-red-400'
    ]
}

