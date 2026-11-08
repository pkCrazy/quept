<div class="mx-auto flex w-full max-w-5xl flex-col gap-8 p-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <flux:button :href="route('debts.index')" variant="ghost" icon="arrow-left" wire:navigate>{{ __('All debts') }}</flux:button>
            <flux:heading size="xl" class="mt-3">{{ $debt->title }}</flux:heading>
            <flux:text>{{ __('Created by :name on :date', ['name' => $debt->owner->name, 'date' => $debt->created_at->format('M j, Y')]) }}</flux:text>
        </div>
        <flux:card class="min-w-56">
            <flux:text>{{ __('Original amount') }}</flux:text>
            <flux:heading size="xl">{{ Number::format((float) $debt->amount, 2) }}</flux:heading>
            <flux:text>{{ __('Repaid :amount · Remaining :remaining', ['amount' => Number::format($repaid, 2), 'remaining' => Number::format($remaining, 2)]) }}</flux:text>
        </flux:card>
    </div>

    @if ($remaining > 0)
        <flux:card class="flex flex-col gap-4">
            <flux:heading size="lg">{{ __('Record a repayment') }}</flux:heading>
            <form wire:submit="addRepayment" class="flex flex-wrap items-end gap-4">
                <flux:field class="w-56">
                    <flux:label>{{ __('Amount paid back') }}</flux:label>
                    <flux:input type="number" min="0.01" step="0.01" wire:model="repaymentAmount" />
                    <flux:error name="repaymentAmount" />
                </flux:field>
                <flux:field class="min-w-72 flex-1">
                    <flux:label>{{ __('Note (optional)') }}</flux:label>
                    <flux:textarea wire:model="repaymentNote" rows="2" maxlength="500" />
                    <flux:error name="repaymentNote" />
                </flux:field>
                <flux:button type="submit" variant="primary">{{ __('Add repayment') }}</flux:button>
            </form>
        </flux:card>
    @else
        <flux:callout variant="success" heading="{{ __('This debt is fully repaid.') }}" />
    @endif

    <section class="flex flex-col gap-3">
        <flux:heading size="lg">{{ __('Repayment history') }}</flux:heading>
        @forelse ($debt->repayments->sortByDesc('created_at') as $repayment)
            <div wire:key="repayment-{{ $repayment->id }}" class="flex items-center justify-between gap-4 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <div>
                    <p class="font-medium">{{ $repayment->user->name }}</p>
                    @if ($repayment->note !== null)
                        <p class="mt-2 whitespace-pre-wrap text-sm text-zinc-700 dark:text-zinc-300">{{ $repayment->note }}</p>
                    @endif
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $repayment->created_at->format('M j, Y · H:i') }}</p>
                </div>
                <p class="font-semibold">{{ Number::format((float) $repayment->amount, 2) }}</p>
            </div>
        @empty
            <flux:card><flux:text>{{ __('No repayments recorded yet.') }}</flux:text></flux:card>
        @endforelse
    </section>

    @if (auth()->id() === $debt->user_id)
        <flux:card class="flex flex-col gap-4">
            <flux:heading size="lg">{{ __('Share this debt') }}</flux:heading>
            <flux:text>{{ __('Invite an existing account by email. They can view the history and add repayments.') }}</flux:text>
            <form wire:submit="invite" class="flex flex-wrap items-end gap-4">
                <flux:field class="w-72">
                    <flux:label>{{ __('Account email') }}</flux:label>
                    <flux:input type="email" wire:model="inviteEmail" />
                    <flux:error name="inviteEmail" />
                </flux:field>
                <flux:button type="submit">{{ __('Share debt') }}</flux:button>
            </form>
            @if ($debt->users->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach ($debt->users as $user)
                        <flux:badge wire:key="shared-user-{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</flux:badge>
                    @endforeach
                </div>
            @endif
        </flux:card>
    @endif
</div>
