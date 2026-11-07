<div class="mx-auto flex w-full max-w-5xl flex-col gap-8 p-6">
    <div>
        <flux:heading size="xl">{{ __('Debts') }}</flux:heading>
        <flux:text>{{ __('Track the original amount and every repayment.') }}</flux:text>
    </div>

    <flux:card class="flex flex-col gap-4">
        <flux:heading size="lg">{{ __('Create a debt') }}</flux:heading>
        <form wire:submit="create" class="grid gap-4 sm:grid-cols-[1fr_12rem_auto] sm:items-end">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="title" placeholder="{{ __('For example, Holiday loan') }}" />
                <flux:error name="title" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Original amount') }}</flux:label>
                <flux:input type="number" min="0.01" step="0.01" wire:model="amount" />
                <flux:error name="amount" />
            </flux:field>
            <flux:button type="submit" variant="primary">{{ __('Create debt') }}</flux:button>
        </form>
    </flux:card>

    <section class="flex flex-col gap-3">
        <flux:heading size="lg">{{ __('Your debts and shared debts') }}</flux:heading>
        @forelse ($debts as $debt)
            @php($repaid = (float) $debt->repayments->sum('amount'))
            <a href="{{ route('debts.show', $debt) }}" wire:navigate class="block rounded-xl border border-neutral-200 p-5 transition hover:border-neutral-400 dark:border-neutral-700 dark:hover:border-neutral-500">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <flux:heading>{{ $debt->title }}</flux:heading>
                        <flux:text>{{ __('Created by :name', ['name' => $debt->owner->name]) }}</flux:text>
                    </div>
                    <div class="text-end text-sm">
                        <p class="font-medium">{{ Number::format($repaid, 2) }} / {{ Number::format((float) $debt->amount, 2) }}</p>
                        <p class="text-zinc-500 dark:text-zinc-400">{{ __('repaid') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <flux:card><flux:text>{{ __('No debts yet. Create one above to get started.') }}</flux:text></flux:card>
        @endforelse
    </section>
</div>
