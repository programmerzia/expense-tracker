<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Today's Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-indigo-600"></div>
                        <div class="pl-4">
                            <div class="text-sm font-medium text-gray-500 mb-1">Today's Expenses</div>
                            <div class="text-2xl font-bold text-indigo-600">${{ number_format($expenseSummaries['today'], 2) }}</div>
                            <div class="text-xs text-gray-500 mt-2">{{ now()->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Current Week -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-emerald-600"></div>
                        <div class="pl-4">
                            <div class="text-sm font-medium text-gray-500 mb-1">This Week</div>
                            <div class="text-2xl font-bold text-emerald-600">${{ number_format($expenseSummaries['current_week'], 2) }}</div>
                            <div class="text-xs text-gray-500 mt-2">{{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M') }}</div>
                        </div>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-purple-600"></div>
                        <div class="pl-4">
                            <div class="text-sm font-medium text-gray-500 mb-1">This Month</div>
                            <div class="text-2xl font-bold text-purple-600">${{ number_format($expenseSummaries['current_month'], 2) }}</div>
                            <div class="text-xs text-gray-500 mt-2">{{ now()->format('F Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Last Month -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 relative">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
                        <div class="pl-4">
                            <div class="text-sm font-medium text-gray-500 mb-1">Last Month</div>
                            <div class="text-2xl font-bold text-blue-600">${{ number_format($expenseSummaries['last_month'], 2) }}</div>
                            <div class="text-xs text-gray-500 mt-2">{{ now()->subMonth()->format('F Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Category Section -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-8">
                <!-- Monthly Expense Chart -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Expense Trend</h3>
                        <div class="relative" style="height: 300px;">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Breakdown</h3>
                        <div class="space-y-4">
                            @foreach($categoryExpenses as $category)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">{{ $category->category }}</span>
                                        <span class="font-medium">${{ number_format($category->total, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($category->total / $categoryExpenses->max('total')) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Active Budgets</h3>
                        <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            New Budget
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($budgets as $budget)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            @if($budget->category)
                                                {{ $budget->category->name }}
                                            @else
                                                Overall Budget
                                            @endif
                                        </h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $budget->period_type === 'monthly' ? 'Monthly' : 'Yearly' }} Budget
                                        </p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Progress</span>
                                        <span>{{ number_format($budget->progress_percentage, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full {{ $budget->status === 'exceeded' ? 'bg-red-600' : ($budget->status === 'warning' ? 'bg-yellow-400' : 'bg-green-600') }}"
                                             style="width: {{ min($budget->progress_percentage, 100) }}%"></div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Budget</p>
                                        <p class="text-lg font-semibold">${{ number_format($budget->amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Spent</p>
                                        <p class="text-lg font-semibold">${{ number_format($budget->spent_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-500 py-8">
                                <p>No active budgets found.</p>
                                <p class="mt-2">Start by creating a new budget to track your expenses!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Long-term Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Last 3 Months -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-500">Last 3 Months</div>
                            <div class="text-xs text-gray-400 mb-2">{{ now()->subMonths(3)->format('M Y') }} - {{ now()->format('M Y') }}</div>
                            <div class="text-2xl font-semibold text-gray-900">${{ number_format($expenseSummaries['last_3_months'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Last 6 Months -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-500">Last 6 Months</div>
                            <div class="text-xs text-gray-400 mb-2">{{ now()->subMonths(6)->format('M Y') }} - {{ now()->format('M Y') }}</div>
                            <div class="text-2xl font-semibold text-gray-900">${{ number_format($expenseSummaries['last_6_months'], 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Last Year -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-500">Last Year</div>
                            <div class="text-xs text-gray-400 mb-2">{{ now()->subYear()->format('M Y') }} - {{ now()->format('M Y') }}</div>
                            <div class="text-2xl font-semibold text-gray-900">${{ number_format($expenseSummaries['last_year'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('expenseChart').getContext('2d');
        const monthlyData = @json($monthlyExpenses);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Monthly Expenses',
                    data: monthlyData.map(item => item.total),
                    borderColor: 'rgb(79, 70, 229)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>