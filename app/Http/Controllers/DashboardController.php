<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $this->currentUserId();
        $account = $userId ? Account::where('user_id', $userId)->first() : null;
        $transactions = $account ? $account->transactions()->latest()->get() : collect();
        $notifications = $userId
            ? User::find($userId)->unreadNotifications()->latest()->take(5)->get()
            : collect();

        return view('dashboard', compact('account', 'transactions', 'notifications'));
    }

    public function getBalance(Request $request)
    {
        $userId = $this->currentUserId();
        $account = $userId ? Account::where('user_id', $userId)->first() : null;

        return response()->json([
            'balance' => $account ? $account->balance : 0.00,
            'minimum_balance' => $account ? $account->minimum_balance : 0.00
        ]);
    }

    public function getStats()
    {
        return response()->json([
            'total_accounts' => Account::count(),
            'total_money' => Account::sum('balance'),
            'total_transactions_today' => \App\Models\Transaction::whereDate('created_at', today())->count(),
        ]);
    }

    private function currentUserId(): ?int
    {
        $userId = Auth::id() ?? User::query()->value('id');

        return $userId === null ? null : (int) $userId;
    }
}