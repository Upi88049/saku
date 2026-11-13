<div class="flex flex-col gap-6">

    <!-- Header & Action Row -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Perencanaan Anggaran (Budget)</h1>
            <p class="text-xs sm:text-sm text-[#5B5F80] mt-1">Kendalikan batas belanja bulanan dan jalankan Ritual Gajian (Payday Wizard).</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button"
                    wire:click="openWizard"
                    class="px-4 py-2.5 rounded-xl border border-[#F26B4B]/30 bg-[#FFE9D6] text-[#8A3A1D] text-xs sm:text-sm font-extrabold shadow-xs hover:bg-[#FFD9C9] transition">
                <span>🪄 Ritual Gajian (Payday Wizard)</span>
            </button>
            <button type="button"
                    wire:click="openSetBudget"
                    class="px-5 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                <span>+ Atur Budget Kategori</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-[#E3F7EC] text-[#0B6B52] border border-[#12A07A]/30 text-xs font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Total Budget Health Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ECEEF7] shadow-card flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <span class="text-xs font-bold text-[#5B5F80] uppercase tracking-wider">Total Anggaran Bulan Ini</span>
            <div class="text-3xl sm:text-4xl font-extrabold text-[#1B1D3A] mt-1" :class="blur ? 'blur-sm select-none' : ''">
                Rp {{ number_format($totalUsed, 0, ',', '.') }} / Rp {{ number_format($totalLimit, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[#5B5F80] mt-1">
                Sisa batas anggaran keseluruhan:
                <strong class="text-[#0B7F63]" :class="blur ? 'blur-sm select-none' : ''">
                    Rp {{ number_format(max(0, $totalLimit - $totalUsed), 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <div class="w-full md:w-64 flex flex-col gap-2">
            @php $totalPct = $totalLimit > 0 ? round(($totalUsed / $totalLimit) * 100) : 0; @endphp
            <div class="flex justify-between text-xs font-bold">
                <span>Tingkat Konsumsi</span>
                <span>{{ $totalPct }}%</span>
            </div>
            <div class="h-3 rounded-full bg-[#E8EAF6] overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $totalPct > 90 ? 'bg-[#D6372F]' : ($totalPct > 70 ? 'bg-[#E0A100]' : 'bg-[#12A07A]') }}"
                     style="width: {{ min(100, $totalPct) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Category Budgets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($budgetRows as $row)
            <div class="bg-white rounded-3xl p-6 border border-[#ECEEF7] shadow-card flex flex-col justify-between min-h-[180px]">
                <div>
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-extrabold text-[#1B1D3A]">{{ $row['name'] }}</h3>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $row['tag_class'] }}">
                            {{ $row['pct'] }}% · {{ $row['tag'] }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-between text-xs font-bold text-[#4A4E70] mb-1.5">
                            <span :class="blur ? 'blur-sm select-none' : ''">Terpakai Rp {{ number_format($row['used'], 0, ',', '.') }}</span>
                            <span :class="blur ? 'blur-sm select-none' : ''">Limit Rp {{ number_format($row['limit'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-[#E8EAF6] overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ min(100, $row['pct']) }}%; background-color: {{ $row['bar_color'] }}"></div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs pt-4 border-t border-[#ECEEF7] mt-4">
                    <span class="text-[#5B5F80]">
                        Sisa: <strong class="text-[#1B1D3A]" :class="blur ? 'blur-sm select-none' : ''">Rp {{ number_format($row['remaining'], 0, ',', '.') }}</strong>
                    </span>
                    <button type="button"
                            wire:click="openSetBudget({{ $row['category_id'] }})"
                            class="text-xs font-bold text-[#4B3FCB] hover:underline">
                        Edit Limit
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-xs text-[#5B5F80]">
                Belum ada kategori pengeluaran.
            </div>
        @endforelse
    </div>

    <!-- Modal Atur Budget Kategori -->
    @if($isFormOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-app border border-[#ECEEF7] flex flex-col gap-4">
            <div class="flex justify-between items-center pb-3 border-b border-[#ECEEF7]">
                <h3 class="text-lg font-extrabold text-[#1B1D3A]">Atur Anggaran Kategori</h3>
                <button type="button" wire:click="$set('isFormOpen', false)" class="text-[#5B5F80] font-bold">✕</button>
            </div>

            <div class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Pilih Kategori</label>
                    <select wire:model="selectedCategoryId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Batas Maksimal Bulanan (Rp)</label>
                    <input type="number" wire:model="monthlyLimit" placeholder="Contoh: 1500000" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                    @error('monthlyLimit') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-[#ECEEF7]">
                <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-[#5B5F80] hover:bg-gray-100">Batal</button>
                <button type="button" wire:click="saveBudget" class="px-5 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6]">Simpan Anggaran</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Ritual Gajian (Payday Wizard) -->
    @if($isWizardOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-app border border-[#ECEEF7] flex flex-col gap-4">
            <div class="flex justify-between items-center pb-3 border-b border-[#ECEEF7]">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🪄</span>
                    <h3 class="text-lg font-extrabold text-[#1B1D3A]">Ritual Gajian (Payday Wizard)</h3>
                </div>
                <button type="button" wire:click="$set('isWizardOpen', false)" class="text-[#5B5F80] font-bold">✕</button>
            </div>

            <p class="text-xs text-[#5B5F80]">
                Saat gajian tiba, bagi dana secara otomatis sesuai persentase keuangan sehat (Aturan 50/20/20/10) dan langsung pisahkan dana tabunganmu.
            </p>

            <div class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nominal Gaji Diterima (Rp)</label>
                    <input type="number" wire:model="salaryAmount" placeholder="Contoh: 8500000" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                    @error('salaryAmount') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Dompet Masuk Gaji</label>
                        <select wire:model="salaryWalletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Dompet Pos Tabungan</label>
                        <select wire:model="savingsWalletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                            @foreach($wallets as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Template Breakdown Preview -->
                <div class="bg-[#F7F8FD] rounded-2xl p-4 border border-[#ECEEF7] flex flex-col gap-2 mt-2">
                    <span class="text-[11px] font-bold text-[#2B2670] uppercase">Simulasi Alokasi Otomatis</span>
                    <div class="flex justify-between items-center text-xs">
                        <span>🏷️ Kebutuhan Pokok (50%)</span>
                        <strong class="font-bold">Rp {{ number_format($salaryAmount * 0.5, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between items-center text-xs text-[#0B7F63]">
                        <span>🛡️ Tabungan / Investasi (20% auto-transfer)</span>
                        <strong class="font-bold">Rp {{ number_format($salaryAmount * 0.2, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span>☕ Keinginan / Jajan (20%)</span>
                        <strong class="font-bold">Rp {{ number_format($salaryAmount * 0.2, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span>🆘 Dana Darurat / Bebas (10%)</span>
                        <strong class="font-bold">Rp {{ number_format($salaryAmount * 0.1, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-[#ECEEF7]">
                <button type="button" wire:click="$set('isWizardOpen', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-[#5B5F80] hover:bg-gray-100">Batal</button>
                <button type="button" wire:click="executeWizard" class="px-5 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6]">Proses Ritual Gajian</button>
            </div>
        </div>
    </div>
    @endif

</div>