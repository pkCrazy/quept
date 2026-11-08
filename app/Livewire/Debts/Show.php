<?php

namespace App\Livewire\Debts;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Show extends Component
{
    public Debt $debt;

    public string $repaymentAmount = '';

    public string $repaymentNote = '';

    public string $inviteEmail = '';

    public function mount(Debt $debt): void
    {
        $this->authorizeAccess($debt);
        $this->debt = $debt;
    }

    public function addRepayment(): void
    {
        $validated = $this->validate([
            'repaymentAmount' => ['required', 'numeric', 'gte:0', 'decimal:0,2', 'max:9999999999.99'],
            'repaymentNote' => ['nullable', 'required_if:repaymentAmount,0', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated): void {
            $debt = Debt::query()->lockForUpdate()->findOrFail($this->debt->id);
            $remaining = (float) $debt->amount - (float) $debt->repayments()->sum('amount');

            if ((float) $validated['repaymentAmount'] > $remaining) {
                throw ValidationException::withMessages([
                    'repaymentAmount' => 'The repayment cannot exceed the remaining balance.',
                ]);
            }

            $debt->repayments()->create([
                'user_id' => Auth::id(),
                'amount' => $validated['repaymentAmount'],
                'note' => filled($validated['repaymentNote']) ? $validated['repaymentNote'] : null,
            ]);
        });

        $this->repaymentAmount = '';
        $this->repaymentNote = '';
        $this->modal('add-repayment')->close();
    }

    public function invite(): void
    {
        $validated = $this->validate([
            'inviteEmail' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::query()->where('email', $validated['inviteEmail'])->firstOrFail();

        if ($user->is(Auth::user())) {
            throw ValidationException::withMessages(['inviteEmail' => 'You already have access to this debt.']);
        }

        $this->debt->users()->syncWithoutDetaching([$user->id]);
        $this->inviteEmail = '';
        $this->modal('share-debt')->close();
    }

    private function authorizeAccess(Debt $debt): void
    {
        abort_unless(
            $debt->user_id === Auth::id() || $debt->users()->whereKey(Auth::id())->exists(),
            403,
        );
    }

    public function render(): View
    {
        $this->authorizeAccess($this->debt);
        $debt = $this->debt->load(['owner', 'users', 'repayments.user']);
        $repaid = (float) $debt->repayments->sum('amount');
        $remaining = max(0, (float) $debt->amount - $repaid);

        return view('livewire.debts.show', compact('debt', 'repaid', 'remaining'))
            ->layout('layouts.app', ['title' => $debt->title]);
    }
}
