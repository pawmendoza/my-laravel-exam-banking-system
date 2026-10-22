<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientFundsException extends RuntimeException
{
    public function __construct(
        public readonly string $accountNumber,
        public readonly string $transactionType,
        public readonly float $attemptedAmount,
        public readonly float $currentBalance,
        public readonly float $minimumBalance,
    ) {
        parent::__construct(
            'Insufficient funds. The transaction would leave your balance below the minimum required balance.'
        );
    }
}
