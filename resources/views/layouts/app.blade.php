<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Saku') }} — Personal Financial Tracker</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[#E4E7F1] text-[#1B1D3A] min-h-screen p-3 sm:p-6"
      x-data="{
          blur: false,
          openTxModal: false,
          openQuickAddModal: false,
          qaText: '',
          qaPreview: null
      }"
      @keydown.window.alt.h.prevent="blur = !blur"
      @open-tx-modal.window="openTxModal = true"
      @close-tx-modal.window="openTxModal = false">

    <!-- Main Card Shell matching Mockup -->
    <div class="max-w-[1392px] mx-auto bg-white rounded-3xl sm:rounded-[28px] overflow-hidden shadow-app flex flex-col lg:flex-row min-h-[900px] border border-[#ECEEF7]">

        <!-- SIDEBAR -->
        <aside class="w-full lg:w-[260px] bg-[#F7F8FD] border-b lg:border-b-0 lg:border-r border-[#ECEEF7] p-6 lg:p-7 flex flex-col justify-between shrink-0">
            <div class="flex flex-col gap-6">
                <!-- App Logo -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-[11px] bg-[#4B3FCB] flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 17l6-6 4 4 8-8M15 7h6v6"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-[#2B2670]">Saku</span>
                    </a>
                    <span class="lg:hidden text-xs font-bold px-2 py-1 rounded-md bg-[#ECEBFC] text-[#4B3FCB]">
                        {{ auth()->user()->plan_tier === 'pro' ? 'Pro' : 'Starter' }}
                    </span>
                </div>

                <!-- MENU UTAMA -->
                <nav aria-label="Menu Utama" class="flex flex-col gap-1">
                    <div class="text-[11px] font-bold text-[#5B5F80] px-3 pb-2 tracking-wider uppercase">Menu Utama</div>

                    @php
                        $navItems = [
                            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z'],
                            ['route' => 'transactions', 'label' => 'Transaksi', 'icon' => 'M8 3 4 7l4 4M4 7h16M16 21l4-4-4-4M20 17H4'],
                            ['route' => 'wallets', 'label' => 'Dompet', 'icon' => 'M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0 0 4h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5'],
                            ['route' => 'debts', 'label' => 'Utang & Piutang', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
                            ['route' => 'budgets', 'label' => 'Budget', 'icon' => 'M21.21 15.89A10 10 0 1 1 8 2.83M22 12A10 10 0 0 0 12 2v10z'],
                        ];
                    @endphp

                    @foreach($navItems as $item)
                        @php $isActive = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ $isActive ? 'bg-white text-[#4B3FCB] shadow-sm font-bold' : 'text-[#4A4E70] hover:bg-white/60 hover:text-[#1B1D3A]' }}">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $item['icon'] }}"></path>
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <!-- PENGATURAN & AKUN -->
                <nav aria-label="Pengaturan" class="flex flex-col gap-1">
                    <div class="text-[11px] font-bold text-[#5B5F80] px-3 pb-2 tracking-wider uppercase">Pengaturan & Akun</div>
                    <a href="{{ route('settings') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('settings') ? 'bg-white text-[#4B3FCB] shadow-sm font-bold' : 'text-[#4A4E70] hover:bg-white/60 hover:text-[#1B1D3A]' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"></path>
                        </svg>
                        <span>Pengaturan</span>
                    </a>
                </nav>
            </div>

            <!-- Remote MCP Status Card -->
            <div class="mt-8 rounded-2xl p-4 bg-gradient-to-br from-[#ECEBFC] to-[#F9E9E3] border border-white/60 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#12A07A]"></span>
                    <span class="text-xs font-bold text-[#0B6B52]">Remote MCP Aktif</span>
                </div>
                <div class="text-xs font-extrabold text-[#1B1D3A]">Asisten AI Siap</div>
                <div class="text-[11px] text-[#4A4E70] leading-relaxed">
                    Koneksi SSE HTTP siap dipakai oleh Claude, Gemini, atau Cursor untuk audit keuangan.
                </div>
                <a href="{{ route('settings') }}" class="mt-1 text-center py-2 px-3 bg-white rounded-xl text-xs font-bold text-[#2B2670] shadow-sm hover:shadow transition">
                    Kelola Token
                </a>
            </div>
        </aside>

        <!-- MAIN VIEWPORT -->
        <div class="flex-1 min-w-0 p-5 sm:p-8 lg:p-9 flex flex-col gap-6">

            <!-- Topbar Row: Quick-Add, Privacy Mode, Catat Transaksi, User Profile -->
            <div class="flex flex-wrap items-center justify-between gap-3 sm:gap-4">

                <!-- Quick-Add Input Component -->
                <div class="flex-1 min-w-[280px] max-w-xl">
                    <livewire:quick-add-bar />
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3 ml-auto">
                    <!-- Privacy / Blur Mode Toggle -->
                    <button type="button"
                            @click="blur = !blur"
                            :title="blur ? 'Tampilkan Nominal (Alt + H)' : 'Samarkan Nominal (Alt + H)'"
                            class="flex items-center gap-2 px-3 sm:px-4 py-2.5 rounded-xl border border-[#E1E4F2] bg-white text-[#2B2670] text-xs sm:text-sm font-bold shadow-sm hover:bg-[#F7F8FD] transition">
                        <!-- Eye Icon -->
                        <template x-if="!blur">
                            <svg class="w-4 h-4 text-[#4B3FCB]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </template>
                        <template x-if="blur">
                            <svg class="w-4 h-4 text-[#D6372F]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </template>
                        <span x-text="blur ? 'Tampilkan' : 'Samarkan'"></span>
                        <span class="hidden sm:inline text-[10px] text-[#5B5F80] font-normal px-1 py-0.5 rounded bg-gray-100">Alt+H</span>
                    </button>

                    <!-- CTA "+ Catat Transaksi" -->
                    <button type="button"
                            @click="$dispatch('open-transaction-modal')"
                            class="flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                        <svg class="w-4 h-4 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                        <span>Catat Transaksi</span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ openMenu: false }">
                        <button @click="openMenu = !openMenu" class="flex items-center gap-2.5 pl-1 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-[#FFD9C9] text-[#8A3A1D] flex items-center justify-center font-extrabold text-sm shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-xs font-extrabold text-[#1B1D3A] leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] font-bold text-[#5B5F80] uppercase tracking-wider">
                                    {{ auth()->user()->plan_tier === 'pro' ? 'Paket Pro' : 'Starter' }}
                                </div>
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="openMenu"
                             @click.outside="openMenu = false"
                             x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-[#ECEEF7] py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs font-bold text-[#1B1D3A]">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-[#5B5F80] truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('settings') }}" class="block px-4 py-2 text-xs font-semibold text-[#4A4E70] hover:bg-[#F7F8FD]">
                                Pengaturan Akun
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-[#D6372F] hover:bg-[#FDE1DF]/40">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Slot Content -->
            <div class="flex-1 flex flex-col gap-6">
                {{ $slot }}
            </div>

        </div>
    </div>

    <!-- Global Transaction Modal with Inline Calculator -->
    <livewire:transaction-modal />

    @livewireScripts
</body>
</html>