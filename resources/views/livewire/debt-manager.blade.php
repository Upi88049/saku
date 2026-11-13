<div class="flex flex-col gap-6">

    <!-- Header & Action Row -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Utang & Piutang</h1>
            <p class="text-xs sm:text-sm text-[#5B5F80] mt-1">Lacak kewajiban utangmu dan tagih piutang talangan via WhatsApp 1-klik.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button"
                    wire:click="openCreate('receivable')"
                    class="px-4 py-2.5 rounded-xl border border-[#12A07A]/30 bg-[#E3F7EC] text-[#0B6B52] text-xs sm:text-sm font-bold shadow-xs hover:bg-[#D0F2E0] transition">
                <span>+ Catat Piutang (Orang Berutang)</span>
            </button>
            <button type="button"
                    wire:click="openCreate('debt')"
                    class="px-4 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                <span>+ Catat Utang (Kamu Berutang)</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-[#E3F7EC] text-[#0B6B52] border border-[#12A07A]/30 text-xs font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-gradient-to-br from-[#E6F7D9] to-[#C9F2DC] rounded-3xl p-6 shadow-card">
            <div class="text-xs font-bold text-[#355C4C]">Total Piutang Aktif (Uangmu di Luar)</div>
            <div class="text-3xl font-extrabold text-[#1B1D3A] mt-1" :class="blur ? 'blur-sm select-none' : ''">
                Rp {{ number_format($totalReceivable, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#355C4C] mt-1">Siap ditagih via link template pesan WhatsApp</div>
        </div>

        <div class="bg-gradient-to-br from-[#FFE9E3] to-[#F9E9E3] rounded-3xl p-6 shadow-card">
            <div class="text-xs font-bold text-[#8A3A1D]">Total Utang Aktif (Kewajibanmu)</div>
            <div class="text-3xl font-extrabold text-[#1B1D3A] mt-1" :class="blur ? 'blur-sm select-none' : ''">
                Rp {{ number_format($totalDebt, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#8A3A1D] mt-1">Dukung pembayaran bertahap (cicilan)</div>
        </div>
    </div>

    <!-- Tabs Switcher -->
    <div class="flex bg-[#F4F5FB] p-1.5 rounded-2xl gap-1.5 max-w-md">
        <button type="button"
                wire:click="$set('activeTab', 'receivable')"
                class="flex-1 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $activeTab === 'receivable' ? 'bg-white text-[#0B6B52] shadow-sm' : 'text-[#5B5F80] hover:text-[#1B1D3A]' }}">
            📥 Piutang (Teman Pinjam / Talangan)
        </button>
        <button type="button"
                wire:click="$set('activeTab', 'debt')"
                class="flex-1 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $activeTab === 'debt' ? 'bg-white text-[#D6372F] shadow-sm' : 'text-[#5B5F80] hover:text-[#1B1D3A]' }}">
            📤 Utang (Kewajiban Kamu)
        </button>
    </div>

    <!-- Items Grid / Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($items as $item)
            @php $status = $item->due_status; @endphp
            <div class="bg-white rounded-3xl p-6 border border-[#ECEEF7] shadow-card flex flex-col justify-between min-h-[200px]">
                <div>
                    <div class="flex justify-between items-start gap-2">
                        <div>
                            <h3 class="text-base font-extrabold text-[#1B1D3A]">{{ $item->contact_name }}</h3>
                            <div class="text-xs text-[#5B5F80] mt-0.5">{{ $item->note }}</div>
                        </div>

                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="text-xs text-[#5B5F80]">Sisa Tagihan</div>
                        <div class="text-2xl font-extrabold text-[#1B1D3A] mt-0.5" :class="blur ? 'blur-sm select-none' : ''">
                            Rp {{ number_format($item->remaining_amount, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] text-[#5B5F80]">
                            Total awal: Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 pt-4 border-t border-[#ECEEF7] mt-4">
                    @if($item->type === 'receivable' && $item->status === 'active')
                        <a href="{{ $item->getWhatsAppReminderUrl() }}"
                           target="_blank"
                           rel="noopener"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#E3F7EC] text-[#0B6B52] hover:bg-[#D0F2E0] text-xs font-bold transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.1-5.6A8.4 8.4 0 1 1 21 11.5z"></path>
                            </svg>
                            <span>Tagih WA</span>
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($item->status === 'active')
                        <button type="button"
                                wire:click="openPayment({{ $item->id }})"
                                class="px-4 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-sm hover:bg-[#3D32B6] transition">
                            Catat Cicilan / Lunas
                        </button>
                    @else
                        <span class="text-xs font-bold text-[#12A07A]">✓ Sudah Lunas</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-xs text-[#5B5F80]">
                Belum ada catatan {{ $activeTab === 'receivable' ? 'piutang' : 'utang' }}.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah Utang / Piutang -->
    @if($isFormOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-app border border-[#ECEEF7] flex flex-col gap-4">
            <div class="flex justify-between items-center pb-3 border-b border-[#ECEEF7]">
                <h3 class="text-lg font-extrabold text-[#1B1D3A]">
                    Tambah Catatan {{ $formType === 'receivable' ? 'Piutang' : 'Utang' }}
                </h3>
                <button type="button" wire:click="$set('isFormOpen', false)" class="text-[#5B5F80] font-bold">✕</button>
            </div>

            <div class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nama Orang / Rekan</label>
                    <input type="text" wire:model="contactName" placeholder="Contoh: Andi, Sari" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                    @error('contactName') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nomor WhatsApp (Opsional, untuk link WA)</label>
                    <input type="text" wire:model="contactPhone" placeholder="Contoh: 081234567890" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nominal (Rp)</label>
                    <input type="number" wire:model="amount" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                    @error('amount') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Tenggat Waktu / Jatuh Tempo</label>
                    <input type="date" wire:model="dueDate" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Keterangan / Catatan</label>
                    <input type="text" wire:model="note" placeholder="Contoh: Talangan makan bakso, Pinjaman" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-[#ECEEF7]">
                <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-[#5B5F80] hover:bg-gray-100">Batal</button>
                <button type="button" wire:click="saveDebt" class="px-5 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6]">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Catat Pembayaran / Pelunasan -->
    @if($isPaymentOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-app border border-[#ECEEF7] flex flex-col gap-4">
            <div class="flex justify-between items-center pb-3 border-b border-[#ECEEF7]">
                <h3 class="text-lg font-extrabold text-[#1B1D3A]">Catat Pembayaran / Cicilan</h3>
                <button type="button" wire:click="$set('isPaymentOpen', false)" class="text-[#5B5F80] font-bold">✕</button>
            </div>

            <div class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nominal yang Dibayar (Rp)</label>
                    <input type="number" wire:model="paymentAmount" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                    @error('paymentAmount') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Pilih Dompet Rekening</label>
                    <select wire:model="paymentWalletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Rp {{ number_format($w->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Tanggal Pembayaran</label>
                    <input type="date" wire:model="paymentDate" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Catatan</label>
                    <input type="text" wire:model="paymentNote" placeholder="Contoh: Cicilan ke-1" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-[#ECEEF7]">
                <button type="button" wire:click="$set('isPaymentOpen', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-[#5B5F80] hover:bg-gray-100">Batal</button>
                <button type="button" wire:click="savePayment" class="px-5 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6]">Simpan Pembayaran</button>
            </div>
        </div>
    </div>
    @endif

</div>