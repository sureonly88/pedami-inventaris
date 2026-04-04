<x-filament-widgets::widget>
    <x-filament::section class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-1">
            <div class="flex flex-col">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-widest flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 text-primary-500" />
                    Monitoring Kontrol Panel
                </h3>
                <p class="text-[10px] text-gray-500 font-medium">Sistem Monitoring Terpadu Koperasi Pedami</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-x-8 gap-y-3 bg-white/50 dark:bg-black/20 p-3 rounded-xl border border-gray-100 dark:border-white/5 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 rounded-full bg-success-500 shadow-sm shadow-success-200 animate-pulse"></div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300">STATUS AMAN</span>
                        <span class="text-[9px] text-gray-500 uppercase">Masa berlaku masih > 1 bulan</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="w-4 h-4 rounded-full bg-warning-500 shadow-sm shadow-warning-200"></div>
                    <div class="flex flex-col leading-tight">
                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300">SEGERA PERPANJANG</span>
                        <span class="text-[9px] text-gray-500 uppercase">Habis dalam 30 hari kedepan</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
