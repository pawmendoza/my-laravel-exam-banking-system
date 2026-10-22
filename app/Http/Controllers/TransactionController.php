<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Exceptions\InsufficientFundsException;
use App\Notifications\LowBalanceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        $userId = $this->currentUserId();
        $account = Account::where('user_id', $userId)->first();
        $transactions = $account ? $account->transactions()->latest()->get() : collect();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $userId = $this->currentUserId();
        $account = Account::where('user_id', $userId)->first();

        return view('transactions.create', compact('account'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:deposit,withdrawal,transfer',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);
        $userId = $this->currentUserId();

        try {
            DB::transaction(function () use ($data, $userId): void {
                $account = Account::where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if (!$account) {
                    $account = Account::create([
                        'user_id' => $userId,
                        'account_number' => 'ACC-' . Str::upper(Str::random(10)),
                        'balance' => 5000.00,
                        'minimum_balance' => 100.00,
                    ]);
                }

                $account->transactions()->create($data + [
                    'reference_number' => (string) Str::uuid(),
                ]);
            });
        } catch (InsufficientFundsException $exception) {
            User::findOrFail($userId)->notify(new LowBalanceNotification(
                accountNumber: $exception->accountNumber,
                transactionType: $exception->transactionType,
                attemptedAmount: $exception->attemptedAmount,
                currentBalance: $exception->currentBalance,
                minimumBalance: $exception->minimumBalance,
            ));

            return back()->withErrors([
                'amount' => $exception->getMessage(),
            ])->withInput();
        }

        return redirect()->route('dashboard')->with('success', 'Transaction successful!');
    }

    private function currentUserId(): int
    {
        $userId = Auth::id() ?? User::query()->value('id');

        abort_if($userId === null, 404, 'No user account exists.');

        return (int) $userId;
    }
}