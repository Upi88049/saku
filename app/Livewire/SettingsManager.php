<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SettingsManager extends Component
{
    public string $name = '';

    public string $email = '';

    public string $currency = 'IDR';

    public int $budgetStartDay = 25;

    public string $paymentInfo = '';

    // Remote MCP Token Management
    public string $tokenName = 'Claude Desktop';

    public string $tokenScope = 'read-write'; // read, read-write

    public ?string $newlyCreatedToken = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->currency = $user->currency ?: 'IDR';
        $this->budgetStartDay = $user->budget_start_day ?: 25;
        $this->paymentInfo = (string) $user->payment_info;
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'budgetStartDay' => 'required|integer|min:1|max:31',
            'paymentInfo' => 'nullable|string|max:255',
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'budget_start_day' => $this->budgetStartDay,
            'payment_info' => $this->paymentInfo,
        ]);

        session()->flash('success', 'Profil dan info pembayaran berhasil disimpan.');
    }

    public function generateMcpToken(): void
    {
        $user = auth()->user();

        // In Phase 1 mock / Sanctum token generator
        $token = 'saku_'.bin2hex(random_bytes(24));
        $this->newlyCreatedToken = $token;

        session()->flash('mcp_success', 'Token Remote MCP berhasil dibuat! Simpan sekarang karena tidak akan ditampilkan lagi.');
    }

    public function render()
    {
        return view('livewire.settings-manager');
    }
}
