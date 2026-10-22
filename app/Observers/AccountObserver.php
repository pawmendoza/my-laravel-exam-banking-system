<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Account;
use App\Exceptions\InsufficientFundsException;

class AccountObserver
{
    public function creating(Transaction $transaction): void
    {
        $account = Account::query()
            ->lockForUpdate()
            ->findOrFail($transaction->account_id);

        if ($transaction->type === 'deposit') {
            $account->balance += $transaction->amount;
        } elseif (in_array($transaction->type, ['withdrawal', 'transfer'])) {
            $newBalance = $account->balance - $transaction->amount;

            if ($newBalance < $account->minimum_balance) {
                throw new InsufficientFundsException(
                    accountNumber: $account->account_number,
                    transactionType: $transaction->type,
                    attemptedAmount: (float) $transaction->amount,
                    currentBalance: (float) $account->balance,
                    minimumBalance: (float) $account->minimum_balance,
                );
            }

            $account->balance = $newBalance;
        }

        $account->save();
    }
}