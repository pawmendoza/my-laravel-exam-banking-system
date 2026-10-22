<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Transactions</h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">New Transaction</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped mb-0">
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
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->reference_number }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>₱{{ number_format((float) $transaction->amount, 2) }}</td>
                                <td>{{ $transaction->notes }}</td>
                                <td>{{ $transaction->created_at }}</td>
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
    </main>
</body>
</html>
