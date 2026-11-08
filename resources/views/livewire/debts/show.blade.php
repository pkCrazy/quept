<div class="mx-auto flex w-full max-w-5xl flex-col gap-8 p-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <flux:button :href="route('debts.index')" variant="ghost" icon="arrow-left" wire:navigate>{{ __('All debts') }}</flux:button>
            <flux:heading size="xl" class="mt-3">{{ $debt->title }}</flux:heading>
            <flux:text>{{ __('Created by :name on :date', ['name' => $debt->owner->name, 'date' => $debt->created_at->format('M j, Y')]) }}</flux:text>
        </div>
        <flux:card class="min-w-56">
            <flux:text>{{ __('Remaining amount') }}</flux:text>
            <flux:heading size="xl">{{ Number::format($remaining, 2) }}</flux:heading>
            <flux:text>{{ __('Repaid :amount · Original :original', ['amount' => Number::format($repaid, 2), 'original' => Number::format((float) $debt->amount, 2)]) }}</flux:text>
        </flux:card>
    </div>

    <div class="flex gap-2">
        @if ($remaining > 0)
            <div>
                <flux:modal.trigger name="add-repayment">
                    <flux:button variant="primary">{{ __('Record a repayment') }}</flux:button>
                </flux:modal.trigger>
            </div>
        @else
            <flux:callout variant="success" heading="{{ __('This debt is fully repaid.') }}" />
        @endif

        @if (auth()->id() === $debt->user_id)
            <div>
                <flux:modal.trigger name="share-debt">
                    <flux:button>{{ __('Share this debt') }}</flux:button>
                </flux:modal.trigger>
            </div>
        @endif
    </div>

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
                <div class="text-end">
                    @if ((float) $repayment->amount === 0.0)
                        <p class="font-semibold">{{ __('No payment') }}</p>
                    @else
                        <p class="font-semibold">{{ Number::format((float) $repayment->amount, 2) }}</p>
                    @endif
                </div>
            </div>
        @empty
            <flux:card><flux:text>{{ __('No repayments recorded yet.') }}</flux:text></flux:card>
        @endforelse
    </section>


    <flux:modal name="add-repayment" class="max-w-lg">
        <form wire:submit="addRepayment" class="flex flex-col gap-6">
            <div>
                <flux:heading size="lg">{{ __('Record a repayment') }}</flux:heading>
                <flux:text>{{ __('Record the amount paid back, or enter 0 with a note to record a missed payment.') }}</flux:text>
            </div>
            <flux:field>
                <flux:label>{{ __('Amount paid back (enter 0 to record a missed payment)') }}</flux:label>
                <flux:input type="number" min="0" step="0.01" wire:model="repaymentAmount" />
                <flux:error name="repaymentAmount" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Note (optional)') }}</flux:label>
                <flux:textarea wire:model="repaymentNote" rows="3" maxlength="500" />
                <flux:error name="repaymentNote" />
            </flux:field>
            <div class="flex justify-end gap-2">
                <flux:modal.close><flux:button variant="filled">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Add repayment') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    @if (auth()->id() === $debt->user_id)
        <flux:modal name="share-debt" class="max-w-lg">
            <form wire:submit="invite" class="flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ __('Share this debt') }}</flux:heading>
                    <flux:text>{{ __('Invite an existing account by email. They can view the history and add repayments.') }}</flux:text>
                </div>
                <flux:field>
                    <flux:label>{{ __('Account email') }}</flux:label>
                    <flux:input type="email" wire:model="inviteEmail" />
                    <flux:error name="inviteEmail" />
                </flux:field>
                @if ($debt->users->isNotEmpty())
                    <div class="flex flex-col gap-2">
                        <flux:text>{{ __('People with access') }}</flux:text>
                        @foreach ($debt->users as $user)
                            <flux:badge wire:key="shared-user-{{ $user->id }}">{{ $user->name }} · {{ $user->email }}</flux:badge>
                        @endforeach
                    </div>
                @endif
                <div class="flex justify-end gap-2">
                    <flux:modal.close><flux:button variant="filled">{{ __('Cancel') }}</flux:button></flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Share debt') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
