<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expense Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="start_date" :value="__('Start Date')" class="mb-1" />
                                <x-text-input 
                                    id="start_date" 
                                    name="start_date" 
                                    type="date" 
                                    class="w-full"
                                    :value="old('start_date', $startDate->format('Y-m-d'))" 
                                    max="{{ $today->format('Y-m-d') }}"
                                    required
                                />
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="end_date" :value="__('End Date')" class="mb-1" />
                                <x-text-input 
                                    id="end_date" 
                                    name="end_date" 
                                    type="date" 
                                    class="w-full"
                                    :value="old('end_date', $endDate->format('Y-m-d'))" 
                                    max="{{ $today->format('Y-m-d') }}"
                                    required
                                />
                            </div>
                            <div class="flex items-end">
                                <x-primary-button type="submit" class="h-[42px]">{{ __('Filter') }}</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">Total Amount: ${{ number_format($totalAmount, 2) }}</h3>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Transactions</th>
                                    <th scope="col" class="px-6 py-3">Total Amount</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $expense)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}
                                        </td>
                                        <td class="px-6 py-4">{{ $expense->transaction_count }}</td>
                                        <td class="px-6 py-4">${{ number_format($expense->total_amount, 2) }}</td>
                                        <td class="px-6 py-4">
                                            <button onclick="showDayDetails('{{ $expense->expense_date }}')"
                                                class="font-medium text-blue-600 hover:underline">View Details</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="4" class="px-6 py-4 text-center">No expenses found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Day Details Modal -->
    <div id="dayDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold" id="modalTitle">Expenses for </h3>
                <button onclick="closeDayDetails()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="mt-2">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showDayDetails(date) {
            fetch(`/reports/day-details?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = `Expenses for ${data.date}`;
                    
                    let content = '<div class="relative overflow-x-auto">';
                    content += '<table class="w-full text-sm text-left text-gray-500">';
                    content += '<thead class="text-xs text-gray-700 uppercase bg-gray-50">';
                    content += '<tr><th class="px-4 py-2">Category</th><th class="px-4 py-2">Description</th><th class="px-4 py-2">Amount</th></tr>';
                    content += '</thead><tbody>';
                    
                    data.expenses.forEach(expense => {
                        content += `<tr class="bg-white border-b">
                            <td class="px-4 py-2">${expense.category.name}</td>
                            <td class="px-4 py-2">${expense.description}</td>
                            <td class="px-4 py-2">$${parseFloat(expense.amount).toFixed(2)}</td>
                        </tr>`;
                    });
                    
                    content += '</tbody></table></div>';
                    document.getElementById('modalContent').innerHTML = content;
                    document.getElementById('dayDetailsModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading expense details');
                });
        }

        function closeDayDetails() {
            document.getElementById('dayDetailsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('dayDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDayDetails();
            }
        });

        // Date validation
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });

        document.getElementById('end_date').addEventListener('change', function() {
            document.getElementById('start_date').max = this.value;
        });

        // Initialize min/max dates
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        if (startDate.value) {
            endDate.min = startDate.value;
        }
        if (endDate.value) {
            startDate.max = endDate.value;
        }
    </script>
    @endpush
</x-app-layout>
