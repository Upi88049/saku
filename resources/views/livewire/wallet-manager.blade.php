<div class="flex flex-col gap-6">

    <!-- Header & Action Row -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Manajemen Dompet & Akun</h1>
            <p class="text-xs sm:text-sm text-[#5B5F80] mt-1">Kelola rekening bank, e-wallet, kas tunai, dan pos tabungan.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button"
                    wire:click="openTransfer"
                    class="px-4 py-2.5 rounded-xl border border-[#E1E4F2] bg-white text-[#2B2670] text-xs sm:text-sm font-bold shadow-xs hover:bg-[#F7F8FD] transition">
                <span>⇄ Transfer Antar Dompet</span>
            </button>
            <button type="button"
                    wire:click="openCreate"
                    class="px-5 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                <span>+ Tambah Dompet</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-[#E3F7EC] text-[#0B6B52] border border-[#12A07A]/30 text-xs font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-[#FDE1DF] text-[#9E2019] border border-[#D6372F]/30 text-xs font-bold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-3xl p-6 border border-[#ECEEF7] shadow-card">
            <div class="text-xs font-bold text-[#5B5F80]">Total Net Worth (Semua Dompet)</div>
            <div class="text-3xl font-extrabold text-[#1B1D3A] mt-1" :class="blur ? 'blur-sm select-none' : ''">
                Rp {{ number_format($totalNet, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#5B5F80] mt-1">{{ $wallets->count() }} dompet terdaftar</div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-[#ECEEF7] shadow-card">
            <div class="text-xs font-bold text-[#5B5F80]">Saldo Aktif untuk Safe-to-Spend</div>
            <div class="text-3xl font-extrabold text-[#0B7F63] mt-1" :class="blur ? 'blur-sm select-none' : ''">
                Rp {{ number_format($totalSts, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-[#5B5F80] mt-1">Tidak termasuk dompet berstatus <em>Exclude from STS</em></div>
        </div>
    </div>

    <!-- Wallets Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($wallets as $w)
            <div class="bg-white rounded-3xl p-6 border border-[#ECEEF7] shadow-card flex flex-col justify-between min-h-[190px] relative">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shadow-xs"
                             style="background-color: {{ $w->color ?: '#4B3FCB' }}">
                            @if($w->type === 'cash') 💵
                            @elseif($w->type === 'bank') 🏦
                            @elseif($w->type === 'ewallet') 📱
                            @elseif($w->type === 'savings') 🛡️
                            @else 💳
                            @endif
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#1B1D3A]">{{ $w->name }}</h3>
                            <span class="text-[11px] font-semibold text-[#5B5F80] uppercase tracking-wider">{{ $w->type }}</span>
                        </div>
                    </div>

                    <button type="button"
                            wire:click="openEdit({{ $w->id }})"
                            class="text-xs font-bold text-[#4B3FCB] hover:underline">
                        Edit / Reconcile
                    </button>
                </div>

                <div class="my-4">
                    <div class="text-xs text-[#5B5F80]">Saldo Terkini</div>
                    <div class="text-2xl font-extrabold text-[#1B1D3A] mt-0.5" :class="blur ? 'blur-sm select-none' : ''">
                        Rp {{ number_format($w->current_balance, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex items-center justify-between text-[11px] pt-3 border-t border-[#ECEEF7]">
                    @if($w->is_excluded_from_sts)
                        <span class="px-2 py-0.5 rounded-md bg-[#FFF1CC] text-[#7A5600] font-bold">
                            Excluded from STS
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-md bg-[#DDF5EC] text-[#0B6B52] font-bold">
                            Termasuk STS
                        </span>
                    @endif

                    @if($w->account_number)
                        <span class="text-[#5B5F80] font-mono">No: {{ $w->account_number }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-xs text-[#5B5F80]">
                Belum ada dompet. Klik <strong>+ Tambah Dompet</strong> untuk membuat.
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah / Edit Dompet -->
    @if($isFormOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-app border border-[#ECEEF7] flex flex-col gap-4">
            <div class="flex justify-between items-center pb-3 border-b border-[#ECEEF7]">
                <h3 class="text-lg font-extrabold text-[#1B1D3A]">
                    {{ $editingWalletId ? 'Edit Dompet / Rekonsiliasi' : 'Tambah Dompet Baru' }}
                </h3>
                <button type="button" wire:click="$set('isFormOpen', false)" class="text-[#5B5F80] font-bold">✕</button>
            </div>

            <div class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nama Dompet</label>
                    <input type="text" wire:model="name" placeholder="Contoh: BCA, GoPay, Kas Dompet" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                    @error('name') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Tipe Akun</label>
                    <select wire:model="type" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                        <option value="bank">Bank</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="cash">Kas Tunai</option>
                        <option value="savings">Tabungan / Dana Darurat</option>
                        <option value="credit">Kartu Kredit / Paylater</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Saldo Terkini (Rp)</label>
                    <input type="number" wire:model="currentBalance" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                    @error('currentBalance') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nomor Rekening / Akun (Opsional)</label>
                    <input type="text" wire:model="accountNumber" placeholder="Contoh: 1234567890" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Pilih Warna</label>
                    <div class="flex gap-2">
                        @foreach(['#4B3FCB', '#12A07A', '#00AED6', '#F26B4B', '#6A3FC4', '#5B5F80'] as $c)
                            <button type="button"
                                    wire:click="$set('color', '{{ $c }}')"
                                    class="w-7 h-7 rounded-full transition {{ $color === $c ? 'ring-2 ring-offset-2 ring-[#4B3FCB]' : '' }}"
                                    style="background-color: {{ $c }}"></button>
                        @endforeach
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="isExcludedFromSts" class="rounded text-[#4B3FCB] focus:ring-0">
                        <span class="font-bold text-[#1B1D3A]">Kecualikan dari Safe-to-Spend (Contoh: Pos Dana Darurat)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-[#ECEEF7]">
                <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-[#5B5F80] hover:bg-gray-100">Batal</button>
                <button type="button" wire:click="saveWallet" class="px-5 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6]">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Transfer Antar Dompet -->
    @if($isTransferOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-app border border-[#ECEEF7] flex flex-col gap-4">
            <div class="flex justify-between items-center pb-3 border-b border-[#ECEEF7]">
                <h3 class="text-lg font-extrabold text-[#1B1D3A]">Transfer Antar Dompet</h3>
                <button type="button" wire:click="$set('isTransferOpen', false)" class="text-[#5B5F80] font-bold">✕</button>
            </div>

            <div class="flex flex-col gap-3 text-xs">
                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Dari Dompet (Asal)</label>
                    <select wire:model="fromWalletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Rp {{ number_format($w->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Ke Dompet (Tujuan)</label>
                    <select wire:model="toWalletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                        @foreach($wallets as $w)
                            @if($w->id != $fromWalletId)
                                <option value="{{ $w->id }}">{{ $w->name }} (Rp {{ number_format($w->current_balance, 0, ',', '.') }})</option>
                            @endif
                        @endforeach
                    </select>
                    @error('toWalletId') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Nominal Transfer (Rp)</label>
                    <input type="number" wire:model="transferAmount" placeholder="Contoh: 200000" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                    @error('transferAmount') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold text-[#4A4E70] mb-1">Catatan</label>
                    <input type="text" wire:model="transferNote" placeholder="Contoh: Topup GoPay dari BCA" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-semibold text-[#1B1D3A]">
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-3 border-t border-[#ECEEF7]">
                <button type="button" wire:click="$set('isTransferOpen', false)" class="px-4 py-2 rounded-xl text-xs font-bold text-[#5B5F80] hover:bg-gray-100">Batal</button>
                <button type="button" wire:click="executeTransfer" class="px-5 py-2 rounded-xl bg-[#4B3FCB] text-white text-xs font-bold shadow-md hover:bg-[#3D32B6]">Proses Transfer</button>
            </div>
        </div>
    </div>
    @endif

</div>