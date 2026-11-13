<div class="relative w-full" x-data="{ focused: false }">
    <!-- Quick-Add Bar Form -->
    <div class="relative flex items-center bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 sm:px-4 py-1.5 focus-within:border-[#4B3FCB] focus-within:ring-2 focus-within:ring-[#4B3FCB]/20 transition shadow-inner">
        <svg class="w-4 h-4 text-[#4B3FCB] shrink-0 mr-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 2 3 14h9l-1 8 10-12h-9z"></path>
        </svg>

        <input type="text"
               wire:model.live.debounce.250ms="query"
               wire:keydown.enter="save"
               @focus="focused = true"
               placeholder="Ketik: Kopi kenangan 24rb gopay"
               class="w-full bg-transparent border-0 p-0 text-xs sm:text-sm font-medium text-[#1B1D3A] focus:ring-0 placeholder:text-[#5B5F80]">

        <span class="hidden sm:inline-flex text-[11px] font-bold text-[#5B5F80] bg-white border border-[#E7E9F5] rounded-lg px-2 py-0.5 ml-2 shadow-xs shrink-0 select-none">
            Quick-Add
        </span>
    </div>

    <!-- Live Preview Floating Banner -->
    @if($preview)
        <div class="absolute left-0 right-0 top-full mt-2 bg-[#F4F3FE] border border-[#DEDBFA] rounded-2xl p-3 shadow-lg z-30 flex flex-wrap items-center justify-between gap-2.5 animate-in fade-in slide-in-from-top-1">
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                <span class="text-[#4B3FCB] uppercase tracking-wider text-[10px]">Pratinjau</span>
                <span class="bg-white px-2.5 py-1 rounded-lg text-[#2B2670] shadow-xs">{{ $preview['type_label'] }}</span>
                <span class="bg-white px-2.5 py-1 rounded-lg text-[#1B1D3A] font-extrabold shadow-xs" :class="blur ? 'blur-sm select-none' : ''">
                    {{ $preview['amount_formatted'] }}
                </span>
                <span class="bg-white px-2.5 py-1 rounded-lg text-[#4A4E70] shadow-xs">{{ $preview['description'] }}</span>
                <span class="bg-white px-2.5 py-1 rounded-lg text-[#4A4E70] shadow-xs">{{ $preview['category_name'] }}</span>
                <span class="bg-white px-2.5 py-1 rounded-lg text-[#4A4E70] shadow-xs">{{ $preview['wallet_name'] }}</span>
            </div>

            <button type="button"
                    wire:click="save"
                    class="ml-auto px-4 py-1.5 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6] transition shrink-0">
                Simpan 1-klik
            </button>
        </div>
    @endif

    @if($statusMessage)
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="absolute left-0 top-full mt-2 text-xs font-bold text-[#12A07A] bg-[#E3F7EC] border border-[#12A07A]/30 rounded-xl px-3 py-1.5 shadow-sm z-30">
            ✓ {{ $statusMessage }}
        </div>
    @endif
</div>