<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saku — Smart Personal Financial Tracker & SaaS Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E4E7F1] text-[#1B1D3A] font-sans antialiased selection:bg-[#4B3FCB] selection:text-white min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <nav class="bg-white/90 backdrop-blur-md rounded-2xl px-6 py-4 flex items-center justify-between shadow-card border border-white/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[12px] bg-[#4B3FCB] flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8M15 7h6v6"></path></svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-[#2B2670]">Saku</span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-[#5B5F80]">
                <a href="#fitur" class="hover:text-[#4B3FCB] transition">Fitur Utama</a>
                <a href="#safe-to-spend" class="hover:text-[#4B3FCB] transition">Safe-to-Spend</a>
                <a href="#harga" class="hover:text-[#4B3FCB] transition">Paket Harga</a>
                <a href="#mcp" class="hover:text-[#4B3FCB] transition">Integrasi AI (MCP)</a>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-sm font-bold shadow-md hover:bg-[#3D32B6] transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold text-[#2B2670] hover:bg-[#F4F3FE] transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-sm font-bold shadow-md hover:bg-[#3D32B6] transition">
                        Daftar Gratis
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#ECEBFC] text-[#4B3FCB] text-xs font-bold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-[#12A07A] animate-pulse"></span>
                SaaS Keuangan Pribadi Generasi Baru
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-[#1B1D3A] leading-[1.15]">
                Kendalikan Keuanganmu <br><span class="text-[#4B3FCB]">Kurang dari 5 Detik.</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-[#5B5F80] font-medium leading-relaxed">
                Bukan cuma melihat pengeluaran masa lalu. Saku memandu keputusan belanja harianmu lewat <strong>Safe-to-Spend</strong>, mencatat struk dengan kalkulator bawaan, membagi <strong>split bill</strong> otomatis ke piutang, dan siap terhubung ke asisten AI pribadimu via <strong>Remote MCP</strong>.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-2xl bg-[#4B3FCB] text-white font-extrabold text-base shadow-xl hover:bg-[#3D32B6] transition transform hover:-translate-y-0.5">
                    Mulai Gratis Sekarang →
                </a>
                <a href="#fitur" class="px-6 py-4 rounded-2xl bg-white border border-[#ECEEF7] text-[#2B2670] font-bold text-base hover:bg-[#F7F8FD] transition shadow-sm">
                    Pelajari Fitur
                </a>
            </div>
        </div>

        <!-- Hero Card Mockup Preview -->
        <div class="bg-white rounded-4xl p-6 sm:p-10 shadow-app border border-[#ECEEF7] max-w-5xl mx-auto">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-[#ECEEF7]">
                <div>
                    <h2 class="text-2xl font-extrabold text-[#1B1D3A]">Ringkasan Keuangan</h2>
                    <p class="text-sm text-[#5B5F80]">Siklus gajian 28 Okt – 27 Nov · sisa 14 hari</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1.5 rounded-full bg-[#E3F7EC] text-[#0B6B52] text-xs font-bold">
                        ● Remote MCP Terhubung
                    </span>
                    <span class="px-3.5 py-1.5 rounded-full bg-[#F4F3FE] text-[#4B3FCB] text-xs font-bold">
                        Paket Pro
                    </span>
                </div>
            </div>

            <!-- 3 Main Hero Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Safe to Spend -->
                <div class="rounded-3xl p-6 bg-gradient-to-br from-[#FFE9D6] via-[#FFD9DE] to-[#E3DDFB] shadow-card flex flex-col justify-between min-h-[220px]">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-[#1B1D3A]">Safe-to-Spend Hari Ini</span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/70 text-[#8A3A1D]">Hero widget</span>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold text-[#1B1D3A] tracking-tight">Rp 150.000</div>
                        <div class="flex justify-between text-xs font-semibold mt-2 text-[#4A3A44]">
                            <span>Terpakai Rp 62.000</span>
                            <span>Sisa Rp 88.000</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-white/70 overflow-hidden mt-1.5">
                            <div class="h-full bg-[#F26B4B] rounded-full" style="width: 41%"></div>
                        </div>
                    </div>
                    <div class="text-xs text-[#5B5F80] leading-snug">
                        (Sisa budget Rp 2.600.000 − tagihan Rp 500.000) ÷ 14 hari
                    </div>
                </div>

                <!-- Total Saldo -->
                <div class="rounded-3xl p-6 bg-gradient-to-br from-[#F3E6D8] to-[#DAD9FB] shadow-card flex flex-col justify-between min-h-[220px]">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-[#1B1D3A]">Total Saldo</span>
                        <span class="text-xs font-bold text-[#2B2670]">4 Dompet</span>
                    </div>
                    <div class="text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Rp 12.480.000</div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white/70 rounded-xl p-2.5">
                            <div class="font-medium text-[#5B5F80]">Bank Utama</div>
                            <div class="font-bold text-[#1B1D3A]">Rp 6.250.000</div>
                        </div>
                        <div class="bg-white/70 rounded-xl p-2.5">
                            <div class="font-medium text-[#5B5F80]">Dana Darurat</div>
                            <div class="font-bold text-[#1B1D3A]">Rp 5.400.000</div>
                        </div>
                    </div>
                </div>

                <!-- Piutang & Utang -->
                <div class="rounded-3xl p-6 bg-gradient-to-br from-[#E6F7D9] to-[#C9F2DC] shadow-card flex flex-col justify-between min-h-[220px]">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-[#1B1D3A]">Utang & Piutang</span>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-[#FDE1DF] text-[#9E2019]">1 Terlambat</span>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-[#355C4C]">Piutang (teman berutang ke kamu)</div>
                        <div class="text-2xl font-extrabold text-[#1B1D3A] mt-0.5">Rp 385.000</div>
                        <div class="text-xs font-medium text-[#355C4C]">3 orang · 1-klik tagih via WA</div>
                    </div>
                    <div class="bg-white/70 rounded-xl p-2.5 flex justify-between items-center text-xs">
                        <div>
                            <div class="text-[#5B5F80]">Utang kamu</div>
                            <div class="font-bold text-[#1B1D3A]">Rp 1.200.000</div>
                        </div>
                        <span class="text-[#5B5F80]">6 cicilan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <section id="fitur" class="py-24">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1B1D3A]">Fitur Unik yang Tidak Ada di Aplikasi Lain</h2>
                <p class="mt-4 text-base text-[#5B5F80]">Didesain khusus untuk mengatasi kebiasaan malas mencatat dan kebocoran halus.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-3xl p-8 border border-[#ECEEF7] shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-[#FFE9D6] text-[#F26B4B] flex items-center justify-center font-bold text-xl mb-6">
                        🎯
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1B1D3A] mb-3">Safe-to-Spend Harian</h3>
                    <p class="text-sm text-[#5B5F80] leading-relaxed">
                        Tahu persis berapa nominal uang aman yang boleh kamu belanjakan hari ini agar gaji tetap cukup sampai tanggal gajian berikutnya.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-3xl p-8 border border-[#ECEEF7] shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-[#ECEBFC] text-[#4B3FCB] flex items-center justify-center font-bold text-xl mb-6">
                        🧮
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1B1D3A] mb-3">Kalkulator Bawaan</h3>
                    <p class="text-sm text-[#5B5F80] leading-relaxed">
                        Tak perlu lagi bolak-balik buka aplikasi kalkulator. Hitung struk belanja, pajak resto, atau diskon langsung di dalam form transaksi.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-3xl p-8 border border-[#ECEEF7] shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-[#E3F7EC] text-[#12A07A] flex items-center justify-center font-bold text-xl mb-6">
                        💬
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1B1D3A] mb-3">Quick-Add Kalimat Natural</h3>
                    <p class="text-sm text-[#5B5F80] leading-relaxed">
                        Cukup ketik kalimat sehari-hari seperti <em>"Kopi kenangan 24rb gopay"</em> dan sistem akan otomatis mem-parsing serta menyimpannya dalam 1 klik.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-3xl p-8 border border-[#ECEEF7] shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-[#F9E9E3] text-[#8A3A1D] flex items-center justify-center font-bold text-xl mb-6">
                        🍕
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1B1D3A] mb-3">Split Bill ke Piutang Otomatis</h3>
                    <p class="text-sm text-[#5B5F80] leading-relaxed">
                        Talangin makan bareng teman? Porsi pribadimu masuk pengeluaran, sisanya otomatis tercatat jadi piutang plus tombol reminder WhatsApp 1-klik!
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-3xl p-8 border border-[#ECEEF7] shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-[#FFF1CC] text-[#7A5600] flex items-center justify-center font-bold text-xl mb-6">
                        ☕
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1B1D3A] mb-3">Detektor Kebocoran Halus</h3>
                    <p class="text-sm text-[#5B5F80] leading-relaxed">
                        Lacak transaksi kecil berulang (*Latte Factor*) seperti kopi harian, biaya admin transfer, dan ongkir yang diam-diam menguras dompetmu.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-3xl p-8 border border-[#ECEEF7] shadow-card">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#ECEBFC] to-[#F9E9E3] text-[#4B3FCB] flex items-center justify-center font-bold text-xl mb-6">
                        🤖
                    </div>
                    <h3 class="text-xl font-extrabold text-[#1B1D3A] mb-3">Remote MCP Server (AI)</h3>
                    <p class="text-sm text-[#5B5F80] leading-relaxed">
                        Hubungkan Claude, Cursor, atau ChatGPT langsung ke catatan keuanganmu secara online via endpoint SSE aman tanpa instalasi lokal yang rumit.
                    </p>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="harga" class="py-20 border-t border-[#ECEEF7]">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1B1D3A]">Harga yang Transparan & Terjangkau</h2>
                <p class="mt-4 text-base text-[#5B5F80]">Mulai gratis selamanya atau upgrade ke Pro untuk fitur tanpa batas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Starter Tier -->
                <div class="bg-white rounded-4xl p-8 sm:p-10 border border-[#ECEEF7] shadow-card flex flex-col justify-between">
                    <div>
                        <div class="inline-block px-3.5 py-1 rounded-full bg-[#F4F5FB] text-xs font-bold text-[#5B5F80] mb-4">
                            PAKET STARTER
                        </div>
                        <h3 class="text-3xl font-extrabold text-[#1B1D3A]">Gratis</h3>
                        <p class="text-sm text-[#5B5F80] mt-2 mb-6">Cocok untuk mulai membiasakan diri mencatat keuangan harian.</p>

                        <ul class="space-y-3.5 text-sm font-semibold text-[#1B1D3A]">
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Hingga 3 Dompet Aktif
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Pencatatan Transaksi & Kalkulator Tanpa Batas
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Safe-to-Spend & Dashboard Analitik
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Utang & Piutang (Maks 5 catatan)
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Quick-Add Bahasa Natural (20x / bulan)
                            </li>
                            <li class="flex items-center gap-3 text-[#5B5F80]">
                                <span class="text-[#5B5F80]">✓</span> Remote MCP AI (Akses Baca 50 req/hari)
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="mt-8 block text-center py-3.5 px-6 rounded-2xl bg-[#F4F5FB] hover:bg-[#E7E9F5] text-[#2B2670] font-bold text-sm transition">
                        Daftar Gratis
                    </a>
                </div>

                <!-- Pro Tier -->
                <div class="bg-gradient-to-b from-[#2B2670] to-[#1B1D3A] text-white rounded-4xl p-8 sm:p-10 shadow-app flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-6 right-6 px-3.5 py-1 rounded-full bg-[#F26B4B] text-xs font-extrabold uppercase tracking-wide">
                        Paling Populer
                    </div>
                    <div>
                        <div class="inline-block px-3.5 py-1 rounded-full bg-white/10 text-xs font-bold text-[#DAD9FB] mb-4">
                            PAKET PRO
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl sm:text-4xl font-extrabold">Rp 29.000</span>
                            <span class="text-sm text-gray-300">/ bulan</span>
                        </div>
                        <p class="text-sm text-gray-300 mt-2 mb-6">Untuk kamu yang butuh kendali penuh dan integrasi AI tingkat lanjut.</p>

                        <ul class="space-y-3.5 text-sm font-semibold">
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> <strong>Dompet Tanpa Batas</strong>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> <strong>Split Bill Otomatis ke Piutang</strong>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Analisis <em>Ghost Expenses</em> Lanjutan
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Quick-Add Bahasa Natural <strong>Tanpa Batas</strong>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> <strong>Remote MCP Server Penuh</strong> (Read & Write)
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#12A07A]">✓</span> Ekspor Laporan CSV & Excel (.xlsx)
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="mt-8 block text-center py-3.5 px-6 rounded-2xl bg-[#4B3FCB] hover:bg-[#3D32B6] text-white font-extrabold text-sm shadow-lg transition">
                        Coba Paket Pro Sekarang
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-20 pt-8 border-t border-[#ECEEF7] flex flex-wrap items-center justify-between gap-4 text-xs font-semibold text-[#5B5F80]">
            <div>
                © {{ date('Y') }} Saku. Personal Financial Tracker & Smart Budgeting SaaS Platform.
            </div>
            <div class="flex items-center gap-6">
                <span>Mode Privasi (Alt + H)</span>
                <span>TALL Stack Powered</span>
                <span>Laravel + Remote MCP</span>
            </div>
        </footer>
    </main>

</body>
</html>