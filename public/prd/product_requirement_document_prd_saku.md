# Product Requirement Document (PRD)
## Saku — Personal Financial Tracker & Smart Budgeting SaaS Platform

---

### 1. Document Overview & Metadata

* **Product Name:** Saku
* **Version:** 1.5.0 (Unified Blueprint: TALL Stack, Multi-Tenant SaaS, Remote MCP HTTP/SSE, & Mockup Reference Integration)
* **Local Workspace Path:** `C:\laragon\www\saku`
* **UI/UX Mockup Reference Path:** `C:\laragon\www\saku\public\mockup\Dashboard-html`
* **Target Release:** Q1 2027
* **Author / Owner:** Product Lead / Saku Engineering
* **Tech Stack Utama:** Laravel Framework + TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire), MySQL/PostgreSQL, Redis, Remote MCP Server (HTTP/SSE), PWA
* **Business Model:** Multi-Tenant SaaS (Freemium: Paket *Starter/Free* & *Pro/Plus*)
* **Status:** Unified Final Blueprint — Ready for Antigravity AI / Engineering Execution

---

### 2. Executive Summary & Problem Statement

#### 2.1 Problem Statement
Sebagian besar individu kesulitan mengontrol kondisi keuangan pribadinya dan sering berhenti menggunakan aplikasi pencatat keuangan konvensional karena:
1. **Friksi Pencatatan Tinggi (Rasa Malas):** Mencatat transaksi manual terasa lambat karena harus membuka aplikasi kalkulator terpisah untuk menghitung struk belanja/pajak, memilih banyak menu, dan kesulitan mencatat saat sedang tidak ada koneksi internet.
2. **Data Pasif (Hanya Melihat Masa Lalu):** Aplikasi biasa hanya menampilkan sisa budget bulanan secara keseluruhan tanpa memberi panduan keputusan berapa batas aman uang yang boleh dibelanjakan *hari ini* agar gaji cukup sampai akhir bulan.
3. **Kewajiban Utang, Piutang, & Split Bill Terlupakan:** Transaksi talangan makan bersama (*split bill*) atau pinjaman antar teman sering tidak tercatat otomatis menjadi piutang, dan pengguna merasa sungkan atau repot menagihnya.
4. **Kebocoran Halus (*Ghost Expenses*) & Tagihan Terlupa:** Biaya admin transfer, ongkir, dan langganan digital otomatis (*auto-debet*) sering tidak disadari hingga saldo menipis.
5. **Privasi Layar di Tempat Umum:** Sebagai aplikasi berbasis web, membuka catatan keuangan di kafe atau kantor berisiko memperlihatkan total saldo kepada orang di sekitar.
6. **Ekosistem AI Terisolasi:** Pengguna yang ingin berkonsultasi dengan asisten AI (seperti Claude, Gemini, atau Cursor) tentang kondisi keuangannya harus mengekspor dan menyalin data transaksi secara manual.

#### 2.2 Solution & Unique Value Proposition (UVP)
Membangun **Saku**, platform **Software as a Service (SaaS)** berbasis web progresif (*Progressive Web App* / PWA) menggunakan **Laravel & Tailwind CSS (TALL Stack)** yang terbuka untuk publik. Platform ini tidak hanya mencatat transaksi kurang dari 5 detik—berkat **Kalkulator Bawaan** dan **Quick-Add Bahasa Natural**—tetapi juga bertindak sebagai asisten keuangan aktif melalui indikator **Safe-to-Spend Harian**, fitur **Split Bill otomatis ke Piutang**, **Ritual Gajian (*Payday Wizard*)**, **Detektor Kebocoran Halus**, **Mode Privasi (*Blur Mode*)**, serta **Remote Model Context Protocol (MCP) Server (HTTP/SSE)** agar klien AI pribadi pengguna dapat terhubung dari mana saja melalui URL endpoint tanpa instalasi lokal yang rumit.

---

### 3. User Personas & SaaS Tiers

#### 3.1 User Personas

| Atribut | Persona A: "The Salaryman" (Dimas, 26) | Persona B: "The Freelancer" (Alya, 30) | Persona C: "The AI-Power User" (Raka, 28) |
| :--- | :--- | :--- | :--- |
| **Pekerjaan & Gaya Hidup** | Karyawan swasta, pengguna setia QRIS, kartu kredit, & langganan digital. | Desainer lepas, pemasukan fluktuatif berbasis termin proyek. | Software engineer/tech enthusiast yang bekerja menggunakan klien AI (Claude, Gemini, Cursor). |
| **Kebutuhan Utama** | Tahu batas jajan harian (*Safe-to-Spend*), bagi gaji otomatis saat gajian, dan patungan makan (*split bill*). | Catat invoice/piutang klien, kirim pengingat bayar via WhatsApp, dan pisahkan pos dana darurat. | Mencatat pengeluaran dan meminta audit keuangan langsung dari jendela *chat* AI via **Remote MCP** secara *online*. |
| **Pain Point Terbesar** | Sering over-budget di minggu ketiga; malas input satu-satu; takut saldo terlihat rekan kerja saat buka web di kantor. | Susah melacak klien yang telat bayar termin dan lupa jadwal tagihan *software* berlangganan. | Malas setup server lokal (`stdio`) atau bolak-balik aplikasi hanya untuk input transaksi dan audit keuangan. |

#### 3.2 SaaS Subscription Tiers (Model Bisnis Publik)

| Fitur & Batasan | Paket Starter (Gratis / Free Tier) | Paket Pro (Berbayar / Premium) |
| :--- | :--- | :--- |
| **Jumlah Dompet / Akun** | Maksimal 3 dompet aktif | **Tidak Terbatas (Unlimited)** |
| **Pencatatan Transaksi & Kalkulator** | Tidak terbatas | Tidak terbatas |
| **Safe-to-Spend & Dashboard Analitik** | Tersedia penuh | Tersedia penuh + Analisis *Ghost Expenses* lanjutan |
| **Utang, Piutang & WA Reminder** | Maksimal 5 catatan aktif | **Tidak Terbatas** + Fitur **Split Bill Otomatis** |
| **Budget Bulanan & Payday Wizard** | Maksimal 4 kategori budget | **Tidak Terbatas** + Template Payday Wizard tersimpan |
| **Quick-Add Bahasa Natural** | 20 kali / bulan | **Tidak Terbatas** |
| **Remote MCP Server (AI Integration)** | Hanya akses baca (`mcp:read` - 50 req/hari) | **Akses Penuh (`mcp:read` & `mcp:write`)** |
| **Ekspor Laporan (CSV / Excel)** | CSV saja | CSV & Excel (`.xlsx`) |

---

### 4. Scope & Feature Hierarchy

```
                       [ SaaS Web App Saku (Laravel + Tailwind CSS) ]
                                             │
  ┌───────────────────┬───────────────────┬──┴────────────────┬───────────────────┬───────────────────┐
  ▼                   ▼                   ▼                   ▼                   ▼                   ▼
[Public Landing &   [Auth, SaaS Plan     [Manajemen          [Transaksi Cerdas   [Utang, Piutang     [Budgeting Aktif,
 SaaS Onboarding]    & Mode Privasi]      Dompet/Akun]        & Kalkulator]       & Split Bill]       Analitik & MCP]
 - Landing Page      - Laravel Auth/SSO   - Multi-wallet      - Inline Calculator - Pinjam/Pemberi    - Safe-to-Spend/Hari
 - Pricing Table     - Plan Enforcement   - Rekening/QR       - Quick-Add Teks    - Split Bill -> Debt- Payday Wizard
 - Interactive Demo  - Blur/Privacy Mode  - Transfer Tx       - Recurring & Bills - WA Reminder Link  - Ghost Expense/Latte
                     - Offline-First PWA                                                              - Remote MCP (SSE)
```

---

### 5. UI/UX Mockup Reference & Design System Alignment

Seluruh antarmuka frontend (Blade + Livewire + Alpine.js + Tailwind CSS) wajib merujuk dan menyelaraskan struktur layout, navigasi, palet warna, tipografi, serta gaya komponen visual dari sumber *mockup* HTML statis di direktori proyek lokal:
* **Direktori Referensi:** `C:\laragon\www\saku\public\mockup\Dashboard-html`

#### 5.1 Aturan Konversi Mockup HTML ke Blade / Livewire
1. **Master Layout (`resources/views/layouts/app.blade.php`):** Mengekstrak struktur *sidebar*, *top navbar*, *mobile bottom navigation* (jika ada di mockup), serta konfigurasi *font* dan palet warna Tailwind dari file utama di `Dashboard-html`.
2. **Komponen Reusabel (`resources/views/components/`):** Memecah elemen berulang pada mockup (seperti kartu statistik/KPI, badge status utang/piutang, tabel transaksi, dan modal) menjadi Blade Components agar konsisten di seluruh halaman.
3. **Slot Fitur Tambahan:** Jika terdapat fitur fungsional dalam PRD ini (seperti *Inline Calculator*, *Quick-Add Bar*, *Safe-to-Spend Hero Card*, *Payday Wizard*, atau halaman *Pengaturan Token Remote MCP*) yang belum memiliki representasi visual eksplisit di folder `Dashboard-html`, tata letaknya wajib mengikuti *design tokens* (border-radius, shadow, padding, dan skala warna) yang digunakan pada file mockup tersebut.

---

### 6. Detailed Functional Requirements

#### 6.1 Public SaaS Landing Page, Autentikasi, & Mode Privasi
* **FR-PUB-01 (Landing Page Publik & Konversi):**
  * Halaman depan publik yang responsif berbasis Tailwind CSS berisi penjelasan nilai jual (*Safe-to-Spend*, Kalkulator Bawaan, Integrasi AI MCP), pratinjau interaktif dashboard, perbandingan paket harga (*Starter* vs *Pro*), FAQ, serta halaman Kebijakan Privasi & Syarat Ketentuan.
* **FR-AUTH-01 (Registrasi Mandiri & SSO):**
  * Pendaftaran terbuka untuk publik menggunakan Email & Password (dengan verifikasi email) serta Google Single Sign-On via **Laravel Socialite**.
  * *Auto-Provisioning:* Saat pengguna baru mendaftar, sistem otomatis membuatkan data bawaan (*default wallets* seperti Tunai & Bank Utama, serta kategori dasar Pemasukan/Pengeluaran) agar pengguna bisa langsung mencatat tanpa konfigurasi kosong.
* **FR-AUTH-02 (Pengaturan Profil, Langganan, & Rekening Penagihan):**
  * Manajemen paket langganan SaaS (upgrade ke *Pro* via payment gateway lokal seperti Midtrans/Xendit).
  * Pengaturan mata uang default (IDR), tanggal awal siklus gajian, tema (*Light / Dark / System* berbasis Tailwind `dark:` class), dan **Info Pembayaran Default** (nomor rekening / e-wallet utama) untuk template penagihan WhatsApp.
  * **Manajemen Koneksi Remote MCP:** Antarmuka untuk menyalin *Remote MCP Endpoint URL* (`https://domainanda.com/mcp/sse`), membuat *Personal Access Token* (via **Laravel Sanctum**), mengatur izin (*Read-Only* vs *Read-Write*), dan mencabut (*revoke*) token kapan saja.
* **FR-PRV-01 (Mode Privasi Sekali Klik / *Blur Mode*):**
  * Tombol ikon mata (*Eye Toggle*) di *header* navigasi (dan pintasan keyboard `Alt + H`) yang menyamarkan seluruh nominal saldo dan transaksi menggunakan kelas utilitas Tailwind (`blur-sm select-none`).

#### 6.2 Manajemen Akun Keuangan (Multi-Wallet)
* **FR-WLT-01:** Pengguna dapat mengelola banyak dompet/akun (Kas Tunai, Bank, E-Wallet, Kartu Kredit/Paylater, Tabungan/Dana Darurat dengan opsi *Exclude from Safe-to-Spend*). Dibatasi sesuai kuota paket SaaS pengguna.
* **FR-WLT-02:** Fitur **Transfer Antar Dompet** yang memindahkan saldo secara atomik tanpa tercatat sebagai *Expense* atau *Income* pada laporan arus kas.
* **FR-WLT-03:** Penyesuaian saldo manual (*reconcile*) dengan pencatatan selisih otomatis.

#### 6.3 Modul Transaksi Cerdas, Kalkulator, & Quick-Add
* **FR-TX-01 (Pencatatan Cepat):** Form input transaksi (Pemasukan, Pengeluaran, Transfer) dengan pemformatan mata uang otomatis (`Rp 150.000`), tanggal & jam, pilihan dompet, kategori, catatan, dan lampiran foto struk (maksimal 2MB via **Laravel Storage**).
* **FR-TX-02 (Kalkulator Bawaan / *Inline Calculator*):**
  * Diimplementasikan menggunakan komponen **Alpine.js + Tailwind CSS** agar bekerja instan di sisi klien (*zero-latency* tanpa bolak-balik *request* ke server saat menekan tombol angka).
  * **Operasi Matematika Dasar:** Mendukung penjumlahan (`+`), pengurangan (`-`), perkalian (`×`), pembagian (`÷`), tombol cepat `000`, `C` (*Clear*), dan `⌫` (*Backspace*).
  * **Live Expression Preview:** Menampilkan ekspresi matematika di atas hasil akhir (contoh: `25.000 + 18.500 × 2` $\rightarrow$ `= Rp 62.000`).
  * **Dukungan Keyboard Fisik & Validasi:** Mendukung input angka/operator langsung dari keyboard desktop dan mencegah penyimpanan jika hasil akhir $\le 0$ atau *error* (`NaN`/`Infinity`).
* **FR-TX-03 (Pencatatan Berbasis Kalimat Natural / *Quick-Add Bar*):**
  * Kolom input cepat bergaya *command bar* di atas dashboard (contoh: `"Kopi kenangan 24rb gopay"` atau `"Bayar utang Budi 100k bca"`).
  * Mem-parsing nominal, keterangan, kategori, dompet, serta jenis transaksi secara otomatis dan menampilkan kartu pratinjau konfirmasi 1-klik.
* **FR-TX-04 (Detektor Langganan & Tagihan Rutin / *Recurring & Subscriptions*):**
  * Dijalankan oleh **Laravel Task Scheduling (Cron)** untuk jadwal tagihan berulang (Spotify, Netflix, kos, listrik, cicilan).
  * Menampilkan jadwal tagihan yang akan jatuh tempo dalam siklus berjalan, memotong kalkulasi *Safe-to-Spend*, serta mengirim pengingat H-2 sebelum auto-debet.
* **FR-TX-05 (Filter & Export):** Filter transaksi multi-kriteria serta ekspor ke CSV dan Excel (`.xlsx` menggunakan **Laravel Excel / Maatwebsite**).

#### 6.4 Modul Utang, Piutang, & Split Bill Otomatis
* **FR-DEBT-01 (Kategori Catatan):** Mencatat **Utang (*Payable*)** dan **Piutang (*Receivable*)** dengan dukungan pelunasan bertahap (cicilan) yang otomatis memperbarui saldo dompet terkait.
* **FR-DEBT-02 (Integrasi *Split Bill* / Patungan ke Piutang):**
  * Tersedia pada form Pengeluaran untuk membagi tagihan talangan secara rata (*equal split*) atau kustom (*exact amount*) untuk diri sendiri dan rekan.
  * **Otomatisasi Sistem (`DB::transaction`):** Memotong saldo dompet sesuai total yang dibayarkan, mencatat porsi pribadi saja ke dalam laporan pengeluaran/budget, dan otomatis membuat catatan **Piutang** atas nama masing-masing teman.
* **FR-DEBT-03 (Indikator Status & Penagihan WhatsApp Satu-Klik):**
  * Indikator visual badge Tailwind: **Hijau** ($> 3$ hari menuju jatuh tempo), **Kuning** ($\le 3$ hari), **Merah** (*Overdue*).
  * **Tombol "Kirim Pengingat WhatsApp":** Membuka tautan `wa.me` berisi template pesan sopan yang otomatis menyertakan rincian nominal, keterangan transaksi, dan info nomor rekening/e-wallet pengguna.

#### 6.5 Modul Pembagian Budget Bulanan & Ritual Gajian (*Payday Wizard*)
* **FR-BDG-01 (Penetapan Anggaran & Progress Tracker):**
  * Batas maksimal pengeluaran bulanan per kategori dengan *progress bar* Tailwind dinamis: Hijau ($0\% - 70\%$), Kuning ($71\% - 90\%$), Merah ($> 90\% - 100\%$), dan Alert Over-budget ($> 100\%$).
* **FR-BDG-02 (Ritual Gajian / *Payday Wizard*):**
  * Muncul otomatis saat pengguna mencatat pemasukan berkategori *"Gaji"* untuk membagi dana ke template pos persentase (misal: 50% Kebutuhan, 20% Tabungan, 20% Keinginan, 10% Dana Darurat) sekaligus membuat transaksi transfer ke dompet tabungan terkait.

#### 6.6 Dashboard Analitik Interaktif & Panduan Keputusan
* **FR-DSH-01 (Kartu "Safe-to-Spend" Harian — *Hero Widget*):**
  * Menampilkan batas maksimal uang aman yang boleh dibelanjakan **hari ini** agar gaji cukup hingga akhir siklus bulanan.
  * **Formula Perhitungan:**
    $$\text{Safe-to-Spend} = \frac{\text{Total Sisa Budget Aktif} - \text{Tagihan Rutin Belum Terbayar}}{\text{Sisa Hari dalam Siklus Bulan Ini}}$$
* **FR-DSH-02 (Key Metrics & Visualisasi Data):**
  * Kartu *Total Net Worth*, Pemasukan, Pengeluaran, Piutang Aktif, Utang Aktif, grafik arus kas harian (*Line Chart*), distribusi kategori (*Donut Chart*), dan widget **Detektor Kebocoran Halus (*Ghost Expenses & Latte Factor*)** untuk transaksi kecil berfrekuensi tinggi.

#### 6.7 Integrasi Ekosistem AI via Remote MCP Server (HTTP / SSE)
* **FR-MCP-01 (Arsitektur Remote MCP Server Multi-Tenant):**
  * Aplikasi mengekspos endpoint **Remote MCP Server** berbasis **HTTP + Server-Sent Events (SSE)** / Streamable HTTP menggunakan paket resmi `laravel/mcp` pada rute terproteksi (misal: `https://api.domainanda.com/mcp`).
  * Klien AI eksternal (Claude Desktop, Cursor, klien MCP web/mobile) dapat terhubung secara *online* dari perangkat mana saja cukup dengan memasukkan URL endpoint dan `Bearer Token` (*Laravel Sanctum*) milik pengguna.
  * *Tenant Isolation:* Setiap sesi SSE otomatis terikat pada `user_id` pemilik token Sanctum sehingga data antar pengguna SaaS terisolasi 100%.
* **FR-MCP-02 (MCP Tools — Aksi Pencatatan & Kueri):**
  * `add_transaction`: Mencatat pemasukan, pengeluaran, atau transfer antar dompet (`type`, `amount`, `category`, `wallet_id`, `date`, `note`).
  * `create_split_bill`: Mencatat transaksi talangan sekaligus membuat catatan piutang untuk beberapa teman secara otomatis.
  * `record_debt_payment`: Mencatat pelunasan atau cicilan utang/piutang dan memperbarui saldo dompet.
  * `get_safe_to_spend`: Mengambil kalkulasi jatah belanja aman hari ini secara *real-time*.
  * `query_transactions`: Mencari dan memfilter riwayat transaksi berdasarkan rentang tanggal, kategori, nominal, atau kata kunci.
* **FR-MCP-03 (MCP Resources — Konteks Data Read-Only):**
  * `finance://summary/monthly`: Ringkasan arus kas bulan berjalan, status budget tiap kategori, dan akumulasi *ghost expenses*.
  * `finance://debts/active`: Daftar seluruh utang dan piutang yang masih aktif beserta status jatuh temponya.
  * `finance://wallets/balances`: Daftar dompet/rekening pengguna beserta saldo terkininya.
* **FR-MCP-04 (MCP Prompts — Template Analisis Terstruktur):**
  * `audit-kebocoran-bulanan`: *Prompt* siap pakai bagi klien AI untuk mengevaluasi pengeluaran kecil berulang dalam 30 hari terakhir.
  * `strategi-pelunasan-utang`: *Prompt* bagi AI untuk menyusun rencana pelunasan utang optimal (*Avalanche* atau *Snowball method*).
* **FR-MCP-05 (Keamanan, Rate Limiting, & Guardrails Remote MCP):**
  * **Rate Limiting SaaS:** Pembatasan jumlah permintaan MCP per menit/hari menggunakan `RateLimiter` Laravel sesuai paket langganan (*Starter* vs *Pro*).
  * **Scope Granular:** Token akses mendukung izin `mcp:read` dan `mcp:write`.
  * **Proteksi Penghapusan Permanen:** Tidak menyediakan MCP *tool* `delete_transaction` untuk mencegah kehilangan data akibat kesalahan perintah AI; penghapusan tetap harus dilakukan manual di antarmuka web.

---

### 7. System Architecture & Tech Stack (Laravel + Tailwind)

* **Backend Framework:** Laravel 11.x / 12.x (PHP 8.3+)
* **Frontend Stack (TALL Stack):**
  * **Tailwind CSS v3/v4:** Sistem desain *utility-first* selaras dengan referensi mockup `public/mockup/Dashboard-html`, mendukung mode gelap (`dark:`), responsif *mobile-first*, dan komponen UI konsisten.
  * **Livewire 3:** Menangani interaktivitas dinamis server-driven (filter transaksi real-time, form split bill dinamis, update dashboard tanpa reload halaman).
  * **Alpine.js:** Menangani interaksi instan di sisi browser (*Inline Calculator*, *Blur Privacy Mode* toggle, modal, dan sinkronisasi antrean PWA offline).
* **Database & Cache:** MySQL 8.0+ / PostgreSQL 16+ serta **Redis** (untuk antrean *queue*, sesi, dan *rate limiting* Remote MCP Server).
* **Authentication & SaaS Billing:** Laravel Breeze/Jetstream + Google Socialite + **Laravel Sanctum** (Bearer Token untuk Remote MCP) + **Laravel Cashier / Midtrans-Xendit SDK** (untuk langganan SaaS).
* **MCP Server Engine:** Paket resmi `laravel/mcp` dengan transport **HTTP / SSE** yang berjalan di belakang Nginx / Laravel Octane (FrankenPHP/Swoole) untuk menangani koneksi SSE konkuren secara efisien.

---

### 8. User Journey & Wireflow

```
[ Public Landing Page (Tailwind CSS) ] ──> [ Daftar Gratis / Login Google SSO ]
                                                       │
                                                       ▼
                              [ Dashboard Utama (Safe-to-Spend & Blur Mode) ]
                                    │
      ┌─────────────────────────────┼─────────────────────────────┬─────────────────────────────┐
      ▼                             ▼                             ▼                             ▼
[ Quick-Add Bar ]         [ + Catat Transaksi ]         [ Utang & Piutang ]         [ Pengaturan Remote MCP ]
 Ketik "Kopi 25rb gopay"   Input via Kalkulator          Klik "Tagih via WA"         Salin URL SSE & Token Sanctum
 -> Konfirmasi 1-Klik      -> Payday Wizard (Gaji)       -> Catat Cicilan            -> Hubungkan ke Claude/Gemini
                           -> Split Bill (Piutang)                                   -> Chat AI catat & audit data
```

---

### 9. Entity Relationship Model (Database Schema Overview)

```
┌───────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│       USERS       │1     *│     WALLETS     │1     *│  TRANSACTIONS   │
├───────────────────┤───────├─────────────────┤───────├─────────────────┤
│ id (PK)           │       │ id (PK)         │       │ id (PK)         │
│ email, password   │       │ user_id (FK)    │       │ user_id (FK)    │
│ name, avatar      │       │ name            │       │ wallet_id (FK)  │
│ plan_tier         │       │ type (cash/bank)│       │ category_id (FK)│
│ plan_expires_at   │       │ current_balance │       │ amount          │
│ currency          │       │ is_excluded_sts │       │ personal_amount │ (Porsi pribadi jika split bill)
│ budget_start_day  │       └─────────────────┘       │ type (in/out/tr)│
│ payment_info      │                                 │ is_recurring    │
└───────────────────┘                                 │ transaction_date│
         │                                            │ note, receipt   │
         │ 1                                          └─────────────────┘
         ├──────────────────────┐                              │ *
         │ 1                    │ 1                            │ 1
         ▼ *                    ▼ *                            │
┌───────────────────┐    ┌─────────────────┐                   │
│    CATEGORIES     │    │ DEBTS_RECEIVABLE│                   │
├───────────────────┤    ├─────────────────┤                   │
│ id (PK)           │    │ id (PK)         │                   │
│ user_id (FK)      │    │ user_id (FK)    │                   │
│ name              │    │ source_tx_id(FK)│                   │
│ type (in/out)     │    │ type (debt/loan)│                   │
│ is_ghost_watch    │    │ contact_name    │                   │
│ icon, color       │    │ total_amount    │1                 *│   DEBT_PAYMENTS   │
└───────────────────┘    │ remaining_amount│───────────────────┤   ├───────────────┤
         │ 1             │ due_date, status│                   │   │ id (PK)       │
         ▼ *             └─────────────────┘                   │   │ debt_id (FK)  │
┌───────────────────┐                                          └───│ tx_id (FK)    │
│      BUDGETS      │                                              │ paid_amount   │
├───────────────────┤                                              │ payment_date  │
│ id (PK)           │    (Catatan: Isolasi data multi-tenant wajib └───────────────┘
│ user_id (FK)      │     menerapkan `user_id` pada seluruh tabel utama &
│ category_id (FK)  │     Laravel Global Scope / Policy)
│ monthly_limit     │
│ alloc_percentage  │
│ month, year       │
└───────────────────┘
```

---

### 10. Non-Functional Requirements (NFR)

* **Isolasi Data Multi-Tenant SaaS (Zero Data Leak):**
  * Setiap tabel transaksional menyertakan kolom `user_id` terindeks dan dilindungi oleh **Laravel Policies** serta query scope ketat sehingga tidak mungkin terjadi kebocoran data lintas pengguna SaaS.
* **Skalabilitas Koneksi Remote MCP (HTTP/SSE):**
  * Infrastruktur produksi direkomendasikan menggunakan **Laravel Octane (FrankenPHP)** dan **Redis** agar koneksi HTTP/SSE *long-lived* dari berbagai klien AI pengguna dapat ditangani dengan penggunaan memori minimal dan latensi $< 200\text{ ms}$.
* **Progressive Web App (PWA) & Offline-First Capability:**
  * Dapat di-*install* ke *home screen* HP/Desktop; pencatatan offline disimpan sementara di `IndexedDB` dan otomatis disinkronkan saat koneksi kembali pulih.
* **Keamanan & Aksesibilitas:**
  * Enkripsi TLS 1.3 (HTTPS wajib untuk keamanan endpoint Remote MCP dan transaksi keuangan), proteksi CSRF/XSS/SQLi bawaan Laravel, serta desain responsif Tailwind CSS (360px hingga 4K) yang memenuhi standar kontras WCAG 2.1 AA.

---

### 11. Release Phasing & MVP Roadmap

#### Fase 1: SaaS Foundation & Core MVP (TALL Stack) - 6 Minggu
* Setup proyek Laravel di `C:\laragon\www\saku` + **Tailwind CSS + Livewire + Alpine.js** + PWA Manifest.
* Konversi desain UI/UX dari `public/mockup/Dashboard-html` menjadi struktur Blade Layout & Komponen Livewire.
* **Public Landing Page**, Registrasi Publik (Email & Google Socialite), *Auto-Provisioning* data awal, dan **Mode Privasi (*Blur Mode*)**.
* Manajemen Dompet (Multi-wallet & transfer antar dompet dengan `DB::transaction`).
* Pencatatan Transaksi lengkap dengan **Kalkulator Bawaan (*Inline Calculator* berbasis Alpine.js)**.
* Indikator **Safe-to-Spend Harian** & Dashboard analitik dasar.
* Modul Utang & Piutang dasar + tombol **Pengingat WhatsApp**.

#### Fase 2: Remote MCP Server, Smart Automation & SaaS Monetization - 4 Minggu
* **Implementasi Remote MCP Server (`laravel/mcp` via HTTP/SSE)** + manajemen token Laravel Sanctum (`mcp:read` / `mcp:write`) + *Rate Limiting* per tier.
* **Quick-Add Bahasa Natural** (input transaksi gaya *chat*).
* Fitur **Split Bill** yang terhubung otomatis ke pencatatan Piutang.
* Budgeting Bulanan dinamis + **Ritual Gajian (*Payday Wizard*)** + **Detektor Kebocoran Halus (*Ghost Expenses*)**.
* Integrasi **Payment Gateway** (Midtrans/Xendit) untuk aktivasi otomatis paket **SaaS Pro**.

#### Fase 3: AI & Ecosystem Expansion - Masa Depan
* **OCR Receipt Scanner:** Pindai foto struk belanja menjadi item transaksi dan pembagian *split bill* otomatis.
* Multi-currency conversion dengan kurs mata uang harian otomatis.
* Sinkronisasi mutasi rekening via Open Finance API.

---

### 12. Appendix: Antigravity AI Synchronization Prompt

Gunakan *prompt* berikut pada **Agent AI Antigravity** di dalam workspace `C:\laragon\www\saku` untuk memindai isi folder `public/mockup/Dashboard-html` dan memperkaya Bagian 5 (UI/UX Mockup Reference) dengan detail komponen aktual dari file HTML lokal:

```markdown
Baca seluruh file HTML, CSS, dan aset UI yang ada di `C:\laragon\www\saku\public\mockup\Dashboard-html`.
Kemudian perbarui bagian "5. UI/UX Mockup Reference & Design System Alignment" pada dokumen PRD ini dengan:
1. Daftar seluruh halaman mockup yang ditemukan beserta pemetaannya ke rute Laravel (`/dashboard`, `/transactions`, `/debts`, `/budgets`, `/wallets`, `/settings`, dll).
2. Detail palet warna aktual (hex/Tailwind classes), tipografi, struktur navigasi (sidebar/header), dan daftar widget yang sudah tergambar di dalam file HTML tersebut.
3. Daftar penyesuaian minor agar fitur Kalkulator Inline, Safe-to-Spend, Split Bill, Payday Wizard, dan Pengaturan Remote MCP tetap konsisten dengan gaya visual mockup tersebut.
```