<?php

namespace App\Livewire\Debts;

use App\Models\Debt;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $title = '';

    public string $amount = '';

    public function create(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2', 'max:9999999999.99'],
        ]);

        $debt = Auth::user()->debts()->create($validated);

        $this->redirectRoute('debts.show', $debt, navigate: true);
    }

    public function render(): View
    {
        $debts = Debt::query()
            ->with(['owner', 'repayments'])
            ->where(function ($query): void {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('users', fn ($users) => $users->whereKey(Auth::id()));
            })
            ->latest()
            ->get();

        return view('livewire.debts.index', compact('debts'))
            ->layout('layouts.app', ['title' => __('Debts')]);
    }
}
