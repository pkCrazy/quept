<?php

use App\Livewire\Debts\Show;
use App\Models\Debt;
use App\Models\User;
use Livewire\Livewire;

it('records a missed payment with a reason and zero paid', function () {
    $user = User::factory()->create();
    $debt = Debt::query()->create([
        'user_id' => $user->id,
        'title' => 'Shared dinner',
        'amount' => 25,
    ]);

    Livewire::actingAs($user)
        ->test(Show::class, ['debt' => $debt])
        ->set('repaymentAmount', '0')
        ->set('repaymentNote', 'Could not pay this week')
        ->call('addRepayment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('repayments', [
        'debt_id' => $debt->id,
        'user_id' => $user->id,
        'amount' => 0,
        'note' => 'Could not pay this week',
    ]);
});

it('requires a reason when no amount was paid', function () {
    $user = User::factory()->create();
    $debt = Debt::query()->create([
        'user_id' => $user->id,
        'title' => 'Shared dinner',
        'amount' => 25,
    ]);

    Livewire::actingAs($user)
        ->test(Show::class, ['debt' => $debt])
        ->set('repaymentAmount', '0')
        ->call('addRepayment')
        ->assertHasErrors(['repaymentNote']);

    $this->assertDatabaseCount('repayments', 0);
});
