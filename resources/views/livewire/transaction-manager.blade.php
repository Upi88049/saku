<div class="flex flex-col gap-6">

    <!-- Header & Action Row -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Riwayat Transaksi</h1>
            <p class="text-xs sm:text-sm text-[#5B5F80] mt-1">Kelola, cari, dan ekspor catatan arus keuanganmu.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button"
                    wire:click="exportCsv"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#E1E4F2] bg-white text-[#2B2670] text-xs sm:text-sm font-bold shadow-xs hover:bg-[#F7F8FD] transition">
                <span>⬇ Ekspor CSV</span>
            </button>
            <button type="button"
                    @click="$dispatch('open-transaction-modal')"
                    class="flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                <span>+ Catat Baru</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-[#E3F7EC] text-[#0B6B52] border border-[#12A07A]/30 text-xs font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white rounded-3xl p-5 border border-[#ECEEF7] shadow-card flex flex-col md:flex-row flex-wrap items-center gap-3">
        <!-- Search Input -->
        <div class="flex-1 min-w-[200px] w-full">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari transaksi..."
                   class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3.5 py-2 text-xs font-medium text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
        </div>

        <!-- Filter Type -->
        <select wire:model.live="filterType"
                class="bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
            <option value="">Semua Tipe</option>
            <option value="expense">Pengeluaran</option>
            <option value="income">Pemasukan</option>
            <option value="transfer">Transfer</option>
        </select>

        <!-- Filter Wallet -->
        <select wire:model.live="filterWallet"
                class="bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
            <option value="">Semua Dompet</option>
            @foreach($wallets as $w)
                <option value="{{ $w->id }}">{{ $w->name }}</option>
            @endforeach
        </select>

        <!-- Filter Category -->
        <select wire:model.live="filterCategory"
                class="bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>

        <!-- Date Range -->
        <div class="flex items-center gap-1.5 text-xs text-[#5B5F80]">
            <input type="date" wire:model.live="startDate" class="bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-2.5 py-2 text-xs font-medium text-[#1B1D3A]">
            <span>–</span>
            <input type="date" wire:model.live="endDate" class="bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-2.5 py-2 text-xs font-medium text-[#1B1D3A]">
        </div>
    </div>

    <!-- Transactions Table / List -->
    <div class="bg-white rounded-3xl border border-[#ECEEF7] shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F7F8FD] border-b border-[#ECEEF7] text-[#5B5F80] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6">Keterangan</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Dompet</th>
                        <th class="py-3.5 px-6 text-right">Nominal</th>
                        <th class="py-3.5 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0F1F8]">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-[#FAFAFE] transition">
                            <td class="py-3.5 px-6 font-semibold text-[#5B5F80] whitespace-nowrap">
                                {{ $t->transaction_date->format('d M Y') }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-[#1B1D3A]">
                                <div class="flex items-center gap-2">
                                    <span>{{ $t->note ?: 'Tanpa keterangan' }}</span>
                                    @if($t->personal_amount && $t->personal_amount != $t->amount)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#F9E9E3] text-[#8A3A1D]">
                                            Split Bill
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-[#4A4E70]">
                                {{ $t->category?->name ?? ($t->type === 'transfer' ? 'Transfer' : '-') }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-[#4A4E70]">
                                {{ $t->wallet?->name }}
                                @if($t->type === 'transfer' && $t->toWallet)
                                    → {{ $t->toWallet->name }}
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right font-extrabold whitespace-nowrap {{ $t->type === 'income' ? 'text-[#0B7F63]' : ($t->type === 'transfer' ? 'text-[#4A4E70]' : 'text-[#1B1D3A]') }}"
                                :class="blur ? 'blur-sm select-none' : ''">
                                {{ $t->type === 'income' ? '+' : ($t->type === 'expense' ? '−' : '') }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <button type="button"
                                        wire:click="deleteTransaction({{ $t->id }})"
                                        wire:confirm="Yakin ingin menghapus transaksi ini? Saldo dompet akan disesuaikan kembali."
                                        class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 font-bold transition"
                                        title="Hapus Transaksi">
                                    🗑
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-[#5B5F80]">
                                Tidak ada transaksi yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-[#ECEEF7]">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>