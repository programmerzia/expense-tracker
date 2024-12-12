<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Budget Management') }}
            </h2>
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Create New Budget') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($budgets as $budget)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        @if($budget->category)
                                            {{ $budget->category->name }}
                                        @else
                                            Overall Budget
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $budget->period_type === 'monthly' ? 'Monthly' : 'Yearly' }} Budget
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('budgets.edit', $budget) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this budget?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
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
                                <div>
                                    <p class="text-gray-500">Remaining</p>
                                    <p class="text-lg font-semibold {{ $budget->remaining_amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format($budget->remaining_amount, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Valid Until</p>
                                    <p class="text-lg font-semibold">{{ $budget->end_date->format('M d, Y') }}</p>
                                </div>
                            </div>

                            @if($budget->notes)
                                <div class="mt-4 text-sm text-gray-500">
                                    <p class="font-medium">Notes:</p>
                                    <p>{{ $budget->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500">
                            <p class="mb-4">No active budgets found.</p>
                            <p>Start by creating a new budget to track your expenses!</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
