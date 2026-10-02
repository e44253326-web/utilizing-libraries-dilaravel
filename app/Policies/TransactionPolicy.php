<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    public function viewHeld(User $user, Transaction $transaction): Response
    {
        return $this->isCashier($user)
            && $transaction->status === 'held'
            && $transaction->user_id === $user->id
                ? Response::allow()
                : Response::denyAsNotFound();
    }

    public function resumeHeld(User $user, Transaction $transaction): Response
    {
        return $this->viewHeld($user, $transaction);
    }

    public function deleteHeld(User $user, Transaction $transaction): Response
    {
        return $this->viewHeld($user, $transaction);
    }

    public function viewReceipt(User $user, Transaction $transaction): Response
    {
        return $this->isCashier($user)
            && $transaction->status === 'paid'
            && $transaction->user_id === $user->id
                ? Response::allow()
                : Response::denyAsNotFound();
    }

    private function isCashier(User $user): bool
    {
        return $user->role === 'cashier';
    }
}
