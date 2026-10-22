<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h2>Banking Dashboard</h2>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">New Transaction</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @foreach($notifications as $notification)
            @if($notification->type === \App\Notifications\LowBalanceNotification::class)
                <div class="alert alert-warning" role="alert">
                    <strong>Low balance warning:</strong>
                    {{ $notification->data['message'] }}
                    <small class="d-block mt-1">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                </div>
            @endif
        @endforeach

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Current Balance</div>
                    <div class="card-body">
                        <h3 class="card-title">₱<span id="account-balance">{{ number_format($account->balance ?? 0, 2) }}</span></h3>
                        <p class="card-text">Minimum Balance: ₱<span id="minimum-balance">{{ number_format($account->minimum_balance ?? 0, 2) }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Recent Transactions</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Reference #</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td>{{ $tx->reference_number }}</td>
                                <td>
                                    <span class="badge bg-{{ $tx->type == 'deposit' ? 'success' : 'danger' }}">
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>
                                <td>₱{{ number_format($tx->amount, 2) }}</td>
                                <td>{{ $tx->notes }}</td>
                                <td>{{ $tx->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- AJAX Polling para sa Real-time Balance Update -->
    <script>
        function fetchBalance() {
            $.ajax({
                url: "{{ route('api.balance') }}",
                type: "GET",
                success: function(response) {
                    $('#account-balance').text(parseFloat(response.balance).toLocaleString('en-US', {minimumFractionDigits: 2}));
                    $('#minimum-balance').text(parseFloat(response.minimum_balance).toLocaleString('en-US', {minimumFractionDigits: 2}));
                }
            });
        }

        // Mag-poll tuwing 3 segundo
        setInterval(fetchBalance, 3000);
    </script>
</body>
</html>