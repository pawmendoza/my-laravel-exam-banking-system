<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $accountNumber,
        public readonly string $transactionType,
        public readonly float $attemptedAmount,
        public readonly float $currentBalance,
        public readonly float $minimumBalance,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => sprintf(
                'Your %s of ₱%s was declined. Your balance is ₱%s and must remain at or above ₱%s.',
                $this->transactionType,
                number_format($this->attemptedAmount, 2),
                number_format($this->currentBalance, 2),
                number_format($this->minimumBalance, 2),
            ),
            'account_number' => $this->accountNumber,
            'transaction_type' => $this->transactionType,
            'attempted_amount' => $this->attemptedAmount,
            'current_balance' => $this->currentBalance,
            'minimum_balance' => $this->minimumBalance,
        ];
    }
}
