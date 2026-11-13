<div x-data="{
        calcExpr: '',
        get calcResult() {
            try {
                if (!this.calcExpr) return 0;
                let clean = this.calcExpr.replace(/×/g, '*').replace(/÷/g, '/');
                if (/[+\-*\/]$/.test(clean)) clean = clean.slice(0, -1);
                let res = Function('return ' + clean)();
                return isFinite(res) && res > 0 ? Math.round(res) : 0;
            } catch(e) {
                return 0;
            }
        },
        append(ch) {
            if (this.calcExpr === '0' && ch !== '.') this.calcExpr = '';
            this.calcExpr += ch;
            this.syncAmount();
        },
        clear() {
            this.calcExpr = '';
            this.syncAmount();
        },
        backspace() {
            this.calcExpr = this.calcExpr.slice(0, -1);
            this.syncAmount();
        },
        syncAmount() {
            $wire.set('amount', this.calcResult);
        }
     }"
     @keydown.window="if ($wire.isOpen && ['0','1','2','3','4','5','6','7','8','9','+','-','*','/','.','Enter','Backspace'].includes($event.key)) {
         if ($event.key === 'Backspace') { backspace(); }
         else if ($event.key === 'Enter') { /* keep */ }
         else if ($event.key === '*') { append('×'); }
         else if ($event.key === '/') { append('÷'); }
         else { append($event.key); }
     }">

    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs animate-in fade-in"
         @click.self="$wire.close()">

        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-app border border-[#ECEEF7] flex flex-col p-6 sm:p-8 animate-in zoom-in-95">

            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-[#ECEEF7]">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#4B3FCB] text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M12 5v14M5 12h14"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-[#1B1D3A]">Catat Transaksi</h2>
                        <p class="text-xs text-[#5B5F80]">Dengan kalkulator bawaan & split bill instan</p>
                    </div>
                </div>

                <button type="button"
                        wire:click="close"
                        class="w-9 h-9 rounded-xl bg-[#F7F8FD] text-[#5B5F80] hover:bg-[#E4E7F1] flex items-center justify-center text-lg font-bold transition">
                    ✕
                </button>
            </div>

            <!-- Transaction Type Switcher -->
            <div class="flex bg-[#F4F5FB] p-1.5 rounded-2xl gap-1.5 mt-5">
                <button type="button"
                        wire:click="setType('expense')"
                        class="flex-1 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $type === 'expense' ? 'bg-white text-[#D6372F] shadow-sm' : 'text-[#5B5F80] hover:text-[#1B1D3A]' }}">
                    💸 Pengeluaran
                </button>
                <button type="button"
                        wire:click="setType('income')"
                        class="flex-1 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $type === 'income' ? 'bg-white text-[#12A07A] shadow-sm' : 'text-[#5B5F80] hover:text-[#1B1D3A]' }}">
                    💰 Pemasukan
                </button>
                <button type="button"
                        wire:click="setType('transfer')"
                        class="flex-1 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $type === 'transfer' ? 'bg-white text-[#4B3FCB] shadow-sm' : 'text-[#5B5F80] hover:text-[#1B1D3A]' }}">
                    🔄 Transfer
                </button>
            </div>

            <!-- Amount & Inline Calculator Area -->
            <div class="mt-6 bg-[#FAFAFE] border border-[#ECEEF7] rounded-2xl p-4 sm:p-5 flex flex-col gap-3">
                <div class="flex items-center justify-between text-xs font-semibold text-[#5B5F80]">
                    <span>Nominal Transaksi</span>
                    <span x-show="calcExpr" class="text-[#4B3FCB] font-bold" x-text="'Ekspresi: ' + calcExpr"></span>
                </div>

                <!-- Big Result Display -->
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-[#5B5F80]">Rp</span>
                    <div class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#1B1D3A]"
                         x-text="calcResult > 0 ? Number(calcResult).toLocaleString('id-ID') : '0'">
                    </div>
                </div>

                <!-- Calculator Keypad (Zero latency Alpine.js) -->
                <div class="grid grid-cols-4 gap-2 pt-2 border-t border-[#ECEEF7]">
                    <button type="button" @click="clear()" class="py-2.5 rounded-xl bg-rose-50 text-[#D6372F] font-bold text-sm hover:bg-rose-100 transition">C</button>
                    <button type="button" @click="backspace()" class="py-2.5 rounded-xl bg-gray-100 text-[#4A4E70] font-bold text-sm hover:bg-gray-200 transition">⌫</button>
                    <button type="button" @click="append('÷')" class="py-2.5 rounded-xl bg-[#ECEBFC] text-[#4B3FCB] font-bold text-sm hover:bg-[#DEDBFA] transition">÷</button>
                    <button type="button" @click="append('×')" class="py-2.5 rounded-xl bg-[#ECEBFC] text-[#4B3FCB] font-bold text-sm hover:bg-[#DEDBFA] transition">×</button>

                    <button type="button" @click="append('7')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">7</button>
                    <button type="button" @click="append('8')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">8</button>
                    <button type="button" @click="append('9')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">9</button>
                    <button type="button" @click="append('-')" class="py-2.5 rounded-xl bg-[#ECEBFC] text-[#4B3FCB] font-bold text-sm hover:bg-[#DEDBFA] transition">−</button>

                    <button type="button" @click="append('4')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">4</button>
                    <button type="button" @click="append('5')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">5</button>
                    <button type="button" @click="append('6')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">6</button>
                    <button type="button" @click="append('+')" class="py-2.5 rounded-xl bg-[#ECEBFC] text-[#4B3FCB] font-bold text-sm hover:bg-[#DEDBFA] transition">+</button>

                    <button type="button" @click="append('1')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">1</button>
                    <button type="button" @click="append('2')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">2</button>
                    <button type="button" @click="append('3')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">3</button>
                    <button type="button" @click="append('000')" class="py-2.5 rounded-xl bg-gray-100 text-[#2B2670] font-bold text-xs hover:bg-gray-200 transition">000</button>

                    <button type="button" @click="append('0')" class="col-span-2 py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">0</button>
                    <button type="button" @click="append('.')" class="py-2.5 rounded-xl bg-white text-[#1B1D3A] font-bold text-sm shadow-xs hover:bg-gray-50 transition border border-[#ECEEF7]">.</button>
                    <button type="button" @click="syncAmount()" class="py-2.5 rounded-xl bg-[#4B3FCB] text-white font-bold text-sm hover:bg-[#3D32B6] transition">=</button>
                </div>
            </div>

            <!-- Form Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
                <!-- Wallet Selection -->
                <div>
                    <label class="block text-xs font-bold text-[#4A4E70] mb-1.5">
                        {{ $type === 'transfer' ? 'Dari Dompet (Asal)' : 'Pilih Dompet' }}
                    </label>
                    <select wire:model="walletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Saldo: Rp {{ number_format($w->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                @if($type === 'transfer')
                    <!-- To Wallet (Transfer only) -->
                    <div>
                        <label class="block text-xs font-bold text-[#4A4E70] mb-1.5">Ke Dompet (Tujuan)</label>
                        <select wire:model="toWalletId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
                            <option value="">-- Pilih Dompet Tujuan --</option>
                            @foreach($wallets as $w)
                                @if($w->id != $walletId)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('toWalletId') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                    </div>
                @else
                    <!-- Category Selection -->
                    <div>
                        <label class="block text-xs font-bold text-[#4A4E70] mb-1.5">Kategori</label>
                        <select wire:model="categoryId" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('categoryId') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                    </div>
                @endif

                <!-- Transaction Date -->
                <div>
                    <label class="block text-xs font-bold text-[#4A4E70] mb-1.5">Tanggal</label>
                    <input type="date" wire:model="transactionDate" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
                </div>

                <!-- Note / Description -->
                <div>
                    <label class="block text-xs font-bold text-[#4A4E70] mb-1.5">Catatan / Keterangan</label>
                    <input type="text" wire:model="note" placeholder="Contoh: Kopi Kenangan, Grab" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A] focus:border-[#4B3FCB] focus:ring-0">
                </div>
            </div>

            <!-- Split Bill Feature (Only for Expense) -->
            @if($type === 'expense')
                <div class="mt-5 p-4 rounded-2xl bg-[#F9E9E3]/50 border border-[#F26B4B]/20 flex flex-col gap-3">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" wire:model.live="isSplitBill" class="rounded text-[#4B3FCB] focus:ring-0 w-4 h-4">
                        <span class="text-xs font-bold text-[#8A3A1D]">🍕 Talangan / Split Bill dengan teman (Otomatis Piutang)</span>
                    </label>

                    @if($isSplitBill)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <div>
                                <label class="block text-[11px] font-bold text-[#5B5F80] mb-1">Porsi Pribadi (Rp)</label>
                                <input type="number" wire:model="personalAmount" placeholder="Berapa bagianmu sendiri" class="w-full bg-white border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                                <span class="text-[10px] text-[#5B5F80]">Hanya porsi ini yang masuk pengeluaranmu</span>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#5B5F80] mb-1">Nama Teman (Pisahkan dengan koma)</label>
                                <input type="text" wire:model="splitFriends" placeholder="Contoh: Andi, Budi atau Andi: 38000, Budi: 50000" class="w-full bg-white border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-bold text-[#1B1D3A]">
                                <span class="text-[10px] text-[#5B5F80]">Otomatis dibuatkan piutang + WA reminder</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Submit Button -->
            <div class="mt-6 pt-4 border-t border-[#ECEEF7] flex items-center justify-end gap-3">
                <button type="button"
                        wire:click="close"
                        class="px-5 py-2.5 rounded-xl border border-[#E1E4F2] text-xs font-bold text-[#5B5F80] hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button"
                        wire:click="save"
                        class="px-6 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                    Simpan Transaksi
                </button>
            </div>

        </div>
    </div>
    @endif
</div>