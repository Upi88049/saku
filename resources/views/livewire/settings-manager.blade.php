<div class="flex flex-col gap-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1B1D3A] tracking-tight">Pengaturan & Akun</h1>
        <p class="text-xs sm:text-sm text-[#5B5F80] mt-1">Kelola preferensi akun, siklus gajian, info rekening penagihan, dan koneksi Remote MCP.</p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-[#E3F7EC] text-[#0B6B52] border border-[#12A07A]/30 text-xs font-bold">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Form Profil & Finansial -->
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ECEEF7] shadow-card flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-[#1B1D3A] mb-4">Profil & Siklus Keuangan</h2>

                <div class="flex flex-col gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A]">
                        @error('name') <span class="text-rose-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Email</label>
                        <input type="email" value="{{ $email }}" disabled class="w-full bg-gray-100 border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Tanggal Mulai Siklus Gajian (1 – 31)</label>
                        <input type="number" wire:model="budgetStartDay" min="1" max="31" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-bold text-[#1B1D3A]">
                        <span class="text-[11px] text-[#5B5F80] mt-0.5">Digunakan untuk menghitung sisa hari pada formula <strong>Safe-to-Spend</strong>.</span>
                    </div>

                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Info Rekening / E-Wallet Default untuk Tagihan WhatsApp</label>
                        <textarea wire:model="paymentInfo" rows="2" placeholder="Contoh: BCA 1234567890 a.n Dimas / GoPay 081234567890" class="w-full bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2.5 text-xs font-semibold text-[#1B1D3A]"></textarea>
                        <span class="text-[11px] text-[#5B5F80] mt-0.5">Teks ini akan otomatis disisipkan saat kamu mengklik tombol <strong>Tagih via WA</strong>.</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#ECEEF7] flex justify-end">
                <button type="button"
                        wire:click="saveProfile"
                        class="px-6 py-2.5 rounded-xl bg-[#4B3FCB] text-white text-xs sm:text-sm font-extrabold shadow-md hover:bg-[#3D32B6] transition">
                    Simpan Perubahan
                </button>
            </div>
        </section>

        <!-- Remote MCP Connection Settings -->
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ECEEF7] shadow-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-extrabold text-[#1B1D3A]">Remote MCP Server (AI)</h2>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-[#E3F7EC] text-[#0B6B52]">
                        Aktif
                    </span>
                </div>

                <p class="text-xs text-[#5B5F80] leading-relaxed mb-4">
                    Hubungkan klien AI pribadimu (seperti <strong>Claude Desktop</strong>, <strong>Cursor</strong>, atau <strong>Gemini</strong>) ke akun Sakumu secara online.
                </p>

                <div class="flex flex-col gap-3 text-xs">
                    <div>
                        <label class="block font-bold text-[#4A4E70] mb-1">Remote MCP Endpoint URL (SSE)</label>
                        <div class="flex items-center gap-2 bg-[#F4F5FB] border border-[#E7E9F5] rounded-xl px-3 py-2 text-xs font-mono font-bold text-[#2B2670]">
                            <span class="truncate">{{ url('/mcp/sse') }}</span>
                        </div>
                    </div>

                    @if($newlyCreatedToken)
                        <div class="p-4 rounded-2xl bg-[#ECEBFC] border border-[#4B3FCB]/30 flex flex-col gap-1.5 animate-in fade-in">
                            <span class="text-[11px] font-bold text-[#4B3FCB] uppercase">Personal Access Token Kamu:</span>
                            <div class="p-2.5 bg-white rounded-xl font-mono text-xs font-extrabold text-[#1B1D3A] break-all select-all border border-[#DEDBFA]">
                                {{ $newlyCreatedToken }}
                            </div>
                            <span class="text-[10px] text-[#D6372F] font-bold mt-1">
                                ⚠️ Salin sekarang. Token ini tidak akan diperlihatkan kembali demi keamanan.
                            </span>
                        </div>
                    @endif

                    <div class="p-4 rounded-2xl bg-[#F7F8FD] border border-[#ECEEF7] flex flex-col gap-2 mt-2">
                        <span class="text-[11px] font-bold text-[#2B2670] uppercase">Contoh Konfigurasi Claude Desktop</span>
                        <pre class="bg-[#1B1D3A] text-[#DAD9FB] p-3 rounded-xl text-[10px] font-mono overflow-x-auto leading-relaxed">{
  "mcpServers": {
    "saku": {
      "url": "{{ url('/mcp/sse') }}",
      "headers": {
        "Authorization": "Bearer YOUR_TOKEN"
      }
    }
  }
}</pre>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#ECEEF7] flex justify-end">
                <button type="button"
                        wire:click="generateMcpToken"
                        class="px-5 py-2.5 rounded-xl border border-[#4B3FCB] bg-[#F4F3FE] text-[#4B3FCB] text-xs font-bold hover:bg-[#ECEBFC] transition shadow-xs">
                    + Buat Token MCP Baru
                </button>
            </div>
        </section>

    </div>

</div>