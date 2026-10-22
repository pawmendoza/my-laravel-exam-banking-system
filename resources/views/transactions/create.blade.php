<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Transaction</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4">New Transaction</h2>

        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700">Transaction Type</label>
                <select name="type" class="w-full border rounded p-2 mt-1" required>
                    <option value="deposit">Deposit</option>
                    <option value="withdrawal">Withdrawal</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Amount</label>
                <input type="number" step="0.01" name="amount" class="w-full border rounded p-2 mt-1" placeholder="0.00" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Notes (Optional)</label>
                <input type="text" name="notes" class="w-full border rounded p-2 mt-1" placeholder="Transaction notes">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
                Submit Transaction
            </button>
        </form>
    </div>
</body>
</html>