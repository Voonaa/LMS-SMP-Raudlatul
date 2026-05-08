<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing MAE Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased p-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Evaluasi MAE (Mean Absolute Error)</h1>
        <p class="text-gray-600 mb-8">Pengujian Akurasi Algoritma Item-Based Collaborative Filtering</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg text-center">
                <p class="text-sm text-blue-600 font-semibold uppercase tracking-wider mb-1">Target User</p>
                <p class="text-2xl font-bold text-blue-900">{{ $userTest->name ?? 'N/A' }}</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-lg text-center">
                <p class="text-sm text-emerald-600 font-semibold uppercase tracking-wider mb-1">Total Data (N)</p>
                <p class="text-4xl font-bold text-emerald-900">{{ $n }}</p>
            </div>
            <div class="bg-rose-50 border border-rose-200 p-6 rounded-lg text-center">
                <p class="text-sm text-rose-600 font-semibold uppercase tracking-wider mb-1">Nilai MAE</p>
                <p class="text-4xl font-bold text-rose-900">{{ number_format($mae, 4) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider border-b border-gray-200">
                        <th class="p-4 font-semibold">Item ID</th>
                        <th class="p-4 font-semibold">Skor Aktual</th>
                        <th class="p-4 font-semibold">Skor Prediksi</th>
                        <th class="p-4 font-semibold">Error Absolut (|Actual - Pred|)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($actualScores ?? [] as $itemId => $actual)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-medium text-gray-900">{{ $itemId }}</td>
                            <td class="p-4">{{ $actual }}</td>
                            <td class="p-4">{{ number_format($predictedScores[$itemId] ?? 0, 4) }}</td>
                            <td class="p-4 text-rose-600 font-semibold">{{ number_format(abs($actual - ($predictedScores[$itemId] ?? 0)), 4) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">Belum ada data interaksi untuk user ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-end">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">Logout Admin</button>
            </form>
        </div>
    </div>
</body>
</html>
