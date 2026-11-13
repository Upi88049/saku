<div class="flex flex-col gap-6">

    <!-- Header Section: Title, Date & Cycle -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Ringkasan Keuangan</h1>
            <div class="text-xs sm:text-sm font-medium text-[#5B5F80] mt-1">
                Siklus gajian {{ $cycleStart->translatedFormat('d M') }} – {{ $cycleEnd->translatedFormat('d M Y') }} · sisa {{ $remainingDays }} hari
            </div>
        </div>
        <div class="text-xs sm:text-sm font-bold text-[#5B5F80]">
            {{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Hero Cards Row (3 Cards: Safe-to-Spend, Total Saldo, Utang & Piutang) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- 1. Safe-to-Spend Hero Card -->
        <section aria-label="Safe to spend hari ini"
                 class="rounded-3xl p-6 bg-gradient-to-br from-[#FFE9D6] via-[#FFD9DE] to-[#E3DDFB] shadow-card flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-[#1B1D3A]">Safe-to-Spend Hari Ini</span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-white/70 text-[#8A3A1D]">Hero widget</span>
            </div>

            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-[#1B1D3A] tracking-tight" :class="blur ? 'blur-sm select-none' : ''">
                    Rp {{ number_format($safeToSpend, 0, ',', '.') }}
                </div>
                <div class="flex justify-between text-xs font-semibold mt-2 text-[#4A3A44]">
                    <span>Terpakai <span :class="blur ? 'blur-sm select-none' : ''">Rp {{ number_format($spentToday, 0, ',', '.') }}</span></span>
                    <span>Sisa <span :class="blur ? 'blur-sm select-none' : ''">Rp {{ number_format($stsRemainingToday, 0, ',', '.') }}</span></span>
                </div>
                <div class="h-2.5 rounded-full bg-white/70 overflow-hidden mt-1.5">
                    <div class="h-full bg-[#F26B4B] rounded-full transition-all duration-500" style="width: {{ $stsPctUsed }}%"></div>
                </div>
            </div>

            <div class="text-xs text-[#5B5F80] leading-snug">
                (Sisa budget <span :class="blur ? 'blur-sm select-none' : ''">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</span> − tagihan <span :class="blur ? 'blur-sm select-none' : ''">Rp {{ number_format($totalUpcomingBills, 0, ',', '.') }}</span>) ÷ {{ $remainingDays }} hari
            </div>
        </section>

        <!-- 2. Total Saldo Card -->
        <section aria-label="Total Saldo"
                 class="rounded-3xl p-6 bg-gradient-to-br from-[#F3E6D8] to-[#DAD9FB] shadow-card flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-[#1B1D3A]">Total Saldo</span>
                <a href="{{ route('wallets') }}" class="text-xs font-bold text-[#2B2670] underline hover:text-[#4B3FCB]">
                    {{ $wallets->count() }} dompet
                </a>
            </div>

            <div class="text-3xl sm:text-4xl font-extrabold text-[#1B1D3A] tracking-tight" :class="blur ? 'blur-sm select-none' : ''">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>

            <!-- Mini wallets breakdown -->
            <div class="grid grid-cols-2 gap-2 mt-auto">
                @foreach($wallets->take(4) as $w)
                    <div class="bg-white/70 backdrop-blur-xs rounded-xl p-2.5">
                        <div class="text-[11px] font-semibold text-[#4A4E70] truncate">{{ $w->name }}</div>
                        <div class="text-xs font-extrabold text-[#1B1D3A] mt-0.5 truncate" :class="blur ? 'blur-sm select-none' : ''">
                            Rp {{ number_format($w->current_balance, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 3. Utang & Piutang Card -->
        <section aria-label="Utang dan Piutang"
                 class="rounded-3xl p-6 bg-gradient-to-br from-[#E6F7D9] to-[#C9F2DC] shadow-card flex flex-col justify-between min-h-[220px]">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-[#1B1D3A]">Utang & Piutang Aktif</span>
                @if($receivableOverdueCount > 0)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-[#FDE1DF] text-[#9E2019]">
                        {{ $receivableOverdueCount }} terlambat
                    </span>
                @else
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-[#DDF5EC] text-[#0B6B52]">
                        Aman
                    </span>
                @endif
            </div>

            <div>
                <div class="text-xs font-semibold text-[#355C4C]">Piutang (orang berutang ke kamu)</div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] mt-0.5" :class="blur ? 'blur-sm select-none' : ''">
                    Rp {{ number_format($totalReceivable, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-[#355C4C] mt-0.5">
                    {{ $receivablesToRemind->count() }} orang · 1-klik tagih WA
                </div>
            </div>

            <div class="bg-white/70 rounded-xl p-2.5 flex justify-between items-center mt-auto text-xs">
                <div>
                    <div class="text-[11px] font-semibold text-[#4A4E70]">Utang kamu</div>
                    <div class="text-sm font-extrabold text-[#1B1D3A]" :class="blur ? 'blur-sm select-none' : ''">
                        Rp {{ number_format($totalDebt, 0, ',', '.') }}
                    </div>
                </div>
                <a href="{{ route('debts') }}" class="text-xs font-bold text-[#0B6B52] underline">
                    Kelola
                </a>
            </div>
        </section>
    </div>

    <!-- Metrics Row: Pemasukan, Pengeluaran, Arus Kas Bersih -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4 border-t border-[#ECEEF7]">
        <!-- Pemasukan -->
        <div class="flex items-center justify-between p-4 bg-[#FAFAFE] rounded-2xl border border-[#ECEEF7]">
            <div>
                <div class="text-xs font-semibold text-[#5B5F80]">Pemasukan Bulan Ini</div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xl font-extrabold text-[#F26B4B]" :class="blur ? 'blur-sm select-none' : ''">
                        Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#E3F7EC] text-[#0B6B52]">Masuk</span>
                </div>
                <div class="text-[11px] text-[#5B5F80] mt-0.5">Bulan {{ Carbon\Carbon::now()->translatedFormat('F') }}</div>
            </div>
            <!-- Mini Bar Chart Simulation -->
            <div class="flex items-end gap-1 h-12" aria-hidden="true">
                <div class="w-2.5 h-4 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-6 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-5 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-9 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-7 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-12 bg-[#F26B4B] rounded-xs shadow-xs"></div>
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="flex items-center justify-between p-4 bg-[#FAFAFE] rounded-2xl border border-[#ECEEF7]">
            <div>
                <div class="text-xs font-semibold text-[#5B5F80]">Pengeluaran Bulan Ini</div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xl font-extrabold text-[#4B3FCB]" :class="blur ? 'blur-sm select-none' : ''">
                        Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#FFF1CC] text-[#7A5600]">Keluar</span>
                </div>
                <div class="text-[11px] text-[#5B5F80] mt-0.5">Total belanja & tagihan</div>
            </div>
            <div class="flex items-end gap-1 h-12" aria-hidden="true">
                <div class="w-2.5 h-6 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-8 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-5 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-10 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-7 bg-[#4B3FCB] rounded-xs shadow-xs"></div>
                <div class="w-2.5 h-8 bg-[#E8EAF6] rounded-xs"></div>
            </div>
        </div>

        <!-- Arus Kas Bersih -->
        <div class="flex items-center justify-between p-4 bg-[#FAFAFE] rounded-2xl border border-[#ECEEF7]">
            <div>
                <div class="text-xs font-semibold text-[#5B5F80]">Arus Kas Bersih</div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xl font-extrabold {{ $netCashFlow >= 0 ? 'text-[#0B7F63]' : 'text-[#D6372F]' }}" :class="blur ? 'blur-sm select-none' : ''">
                        Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $netCashFlow >= 0 ? 'bg-[#E3F7EC] text-[#0B6B52]' : 'bg-[#FDE1DF] text-[#9E2019]' }}">
                        {{ $netCashFlow >= 0 ? 'Surplus' : 'Defisit' }}
                    </span>
                </div>
                <div class="text-[11px] text-[#5B5F80] mt-0.5">Pemasukan − Pengeluaran</div>
            </div>
            <div class="flex items-end gap-1 h-12" aria-hidden="true">
                <div class="w-2.5 h-5 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-7 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-6 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-8 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-9 bg-[#E8EAF6] rounded-xs"></div>
                <div class="w-2.5 h-12 bg-[#12B58B] rounded-xs shadow-xs"></div>
            </div>
        </div>
    </div>

    <!-- Row: Budget Bulanan + Kebocoran Halus -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        <!-- Budget Bulanan (3 cols) -->
        <section class="lg:col-span-3 bg-[#FAFAFE] border border-[#ECEEF7] rounded-3xl p-6 flex flex-col gap-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-extrabold text-[#1B1D3A]">Budget Bulanan</h2>
                <a href="{{ route('budgets') }}" class="text-xs font-bold text-[#4B3FCB] hover:underline">Atur budget</a>
            </div>

            @if(count($budgetSummary) > 0)
                <div class="flex flex-col gap-4">
                    @foreach($budgetSummary as $b)
                        <div>
                            <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                                <span class="text-[#1B1D3A]">{{ $b['name'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[#4A4E70]" :class="blur ? 'blur-sm select-none' : ''">
                                        Rp {{ number_format($b['used'], 0, ',', '.') }} / Rp {{ number_format($b['limit'], 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $b['tag_class'] }}">
                                        {{ $b['pct'] }}% · {{ $b['tag'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="h-2.5 rounded-full bg-[#E8EAF6] overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="width: {{ min(100, $b['pct']) }}%; background-color: {{ $b['bar_color'] }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-xs text-[#5B5F80]">
                    Belum ada anggaran bulan ini. <a href="{{ route('budgets') }}" class="text-[#4B3FCB] font-bold underline">Buat sekarang</a>
                </div>
            @endif
        </section>

        <!-- Kebocoran Halus (Ghost Expenses) (2 cols) -->
        <section class="lg:col-span-2 bg-[#FAFAFE] border border-[#ECEEF7] rounded-3xl p-6 flex flex-col justify-between gap-4">
            <div>
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-extrabold text-[#1B1D3A]">Kebocoran Halus</h2>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-[#FFF1CC] text-[#7A5600]">
                        {{ $ghostPct }}% pengeluaran
                    </span>
                </div>
                <div class="text-xs font-medium text-[#5B5F80] mt-1">Transaksi kecil berulang bulan ini</div>
                <div class="text-2xl font-extrabold text-[#1B1D3A] mt-1" :class="blur ? 'blur-sm select-none' : ''">
                    Rp {{ number_format($totalGhost, 0, ',', '.') }}
                </div>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($ghostExpenses as $g)
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-[#1B1D3A]">{{ $g->note }} <span class="text-[#5B5F80] font-normal">· {{ $g->count }}×</span></span>
                            <span class="font-bold text-[#1B1D3A]" :class="blur ? 'blur-sm select-none' : ''">
                                Rp {{ number_format($g->total, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="h-2 rounded-full bg-[#E8EAF6] overflow-hidden">
                            <div class="h-full bg-[#4B3FCB] rounded-full" style="width: {{ $totalGhost > 0 ? ($g->total / $totalGhost) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-[#5B5F80] py-4 text-center">
                        Tidak ada transaksi kecil mencurigakan bulan ini.
                    </div>
                @endforelse
            </div>

            <div class="text-[11px] text-[#4A4E70] leading-relaxed pt-2 border-t border-[#ECEEF7]">
                💡 Mengurangi jajan kecil 3× dalam sebulan akan otomatis menambah jatah <strong>Safe-to-Spend</strong> harianmu!
            </div>
        </section>
    </div>

    <!-- Row: Transaksi Terbaru + Tagihan & Piutang -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        <!-- Transaksi Terbaru (3 cols) -->
        <section class="lg:col-span-3 bg-[#FAFAFE] border border-[#ECEEF7] rounded-3xl p-6 flex flex-col gap-3">
            <div class="flex justify-between items-center pb-2 border-b border-[#ECEEF7]">
                <h2 class="text-lg font-extrabold text-[#1B1D3A]">Transaksi Terbaru</h2>
                <a href="{{ route('transactions') }}" class="text-xs font-bold text-[#4B3FCB] hover:underline">Lihat semua</a>
            </div>

            <div class="flex flex-col divide-y divide-[#F0F1F8]">
                @forelse($recentTransactions as $tx)
                    <div class="flex items-center gap-3.5 py-3">
                        <!-- Icon Bubble -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-base
                            {{ $tx->type === 'income' ? 'bg-[#E3F7EC] text-[#0B6B52]' : ($tx->type === 'transfer' ? 'bg-[#EDEFF8] text-[#4A4E70]' : 'bg-[#FFE9E3] text-[#B8431F]') }}">
                            @if($tx->type === 'income')
                                ↗
                            @elseif($tx->type === 'transfer')
                                ⇄
                            @else
                                ↘
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#1B1D3A] truncate">{{ $tx->note ?: 'Tanpa catatan' }}</div>
                            <div class="text-[11px] text-[#5B5F80] mt-0.5 truncate">
                                {{ $tx->category?->name ?? 'Transfer' }} · {{ $tx->wallet?->name }}
                                @if($tx->type === 'transfer') → {{ $tx->toWallet?->name }} @endif
                                · {{ Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M') }}
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="text-sm font-extrabold whitespace-nowrap {{ $tx->type === 'income' ? 'text-[#0B7F63]' : ($tx->type === 'transfer' ? 'text-[#4A4E70]' : 'text-[#1B1D3A]') }}"
                             :class="blur ? 'blur-sm select-none' : ''">
                            {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '−' : '') }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-[#5B5F80]">
                        Belum ada transaksi. Klik <strong>+ Catat Transaksi</strong> untuk mencatat.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Tagihan & Piutang Column (2 cols) -->
        <div class="lg:col-span-2 flex flex-col gap-5">

            <!-- Tagihan Mendatang (Recurring Bills) -->
            <section class="bg-[#FAFAFE] border border-[#ECEEF7] rounded-3xl p-6 flex flex-col gap-3">
                <div class="flex justify-between items-center pb-2 border-b border-[#ECEEF7]">
                    <h2 class="text-base font-extrabold text-[#1B1D3A]">Tagihan Mendatang</h2>
                    <span class="text-xs font-bold text-[#5B5F80]" :class="blur ? 'blur-sm select-none' : ''">
                        Rp {{ number_format($totalUpcomingBills, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex flex-col divide-y divide-[#F0F1F8]">
                    @forelse($upcomingBills as $bill)
                        <div class="flex items-center justify-between py-2.5">
                            <div>
                                <div class="text-xs font-bold text-[#1B1D3A]">{{ $bill->note }}</div>
                                <div class="text-[11px] text-[#5B5F80]">{{ $bill->wallet?->name }} · {{ Carbon\Carbon::parse($bill->transaction_date)->diffForHumans() }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-extrabold text-[#1B1D3A]" :class="blur ? 'blur-sm select-none' : ''">
                                    Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] font-bold text-[#4B3FCB]">Auto-cut</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-[#5B5F80] py-3 text-center">
                            Tidak ada tagihan tertunda bulan ini.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Piutang Perlu Ditagih (WhatsApp Reminder) -->
            <section class="bg-[#FAFAFE] border border-[#ECEEF7] rounded-3xl p-6 flex flex-col gap-3">
                <div class="flex justify-between items-center pb-2 border-b border-[#ECEEF7]">
                    <h2 class="text-base font-extrabold text-[#1B1D3A]">Piutang Perlu Ditagih</h2>
                    <a href="{{ route('debts') }}" class="text-xs font-bold text-[#4B3FCB] hover:underline">Semua</a>
                </div>

                <div class="flex flex-col divide-y divide-[#F0F1F8]">
                    @forelse($receivablesToRemind as $debt)
                        @php $status = $debt->due_status; @endphp
                        <div class="flex items-center justify-between py-2.5 gap-2">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-[#1B1D3A] truncate">
                                    {{ $debt->contact_name }}
                                    <span class="font-extrabold" :class="blur ? 'blur-sm select-none' : ''">
                                        Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                    <span class="text-[10px] text-[#5B5F80] truncate">{{ $debt->note }}</span>
                                </div>
                            </div>

                            <a href="{{ $debt->getWhatsAppReminderUrl() }}"
                               target="_blank"
                               rel="noopener"
                               class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#E3F7EC] text-[#0B6B52] hover:bg-[#D0F2E0] text-[11px] font-bold transition shadow-xs">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.1-5.6A8.4 8.4 0 1 1 21 11.5z"></path>
                                </svg>
                                <span>Tagih WA</span>
                            </a>
                        </div>
                    @empty
                        <div class="text-xs text-[#5B5F80] py-3 text-center">
                            Tidak ada piutang aktif saat ini.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

</div>