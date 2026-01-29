@extends('layouts.app')

@section('title', 'Top Distributors')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Top Distributors</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ranking of distributors by total commission earned</p>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <form id="filterForm" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date From</label>
                <input type="date" 
                       id="date_from" 
                       name="date_from"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date To</label>
                <input type="date" 
                       id="date_to" 
                       name="date_to"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="distributor_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Distributor Name</label>
                <input type="text" 
                       id="distributor_name" 
                       name="distributor_name"
                       placeholder="Search by name or username"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Apply Filter
                </button>
                <button type="button" 
                        id="clearFilters"
                        class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Clear
                </button>
            </div>
        </form>
    </div>

    <!-- Loading State -->
    <div id="loading" class="hidden text-center py-12">
        <svg class="animate-spin h-12 w-12 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Loading...</p>
    </div>

    <!-- Summary Cards -->
    <div id="summaryCards" class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
        <!-- Cards will be inserted here -->
    </div>

    <!-- Results Table -->
    <div id="resultsContainer" class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div id="resultsCount" class="mb-4 text-sm text-gray-500 dark:text-gray-400"></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Top</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Distributor's Name</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prevMobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Previous
                    </button>
                    <button id="nextMobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p id="paginationInfo" class="text-xs text-gray-700 dark:text-gray-300">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav id="paginationButtons" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <!-- Pagination buttons will be inserted here -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const clearBtn = document.getElementById('clearFilters');
    const loading = document.getElementById('loading');
    const resultsContainer = document.getElementById('resultsContainer');
    const summaryCards = document.getElementById('summaryCards');
    const resultsCount = document.getElementById('resultsCount');
    const tableBody = document.getElementById('tableBody');

    // Pagination variables
    let allRecords = [];
    let currentPage = 1;
    const recordsPerPage = 10;

    // Load data on page load
    loadData();

    // Filter form submit
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadData();
    });

    // Clear filters
    clearBtn.addEventListener('click', function() {
        filterForm.reset();
        currentPage = 1;
        loadData();
    });

    // Load top distributors data
    function loadData() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value.trim()) {
                params.append(key, value);
            }
        }

        loading.classList.remove('hidden');
        resultsContainer.classList.add('hidden');
        summaryCards.classList.add('hidden');

        fetch(`/api/v1/top-distributors?${params}`)
            .then(response => response.json())
            .then(data => {
                loading.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
                summaryCards.classList.remove('hidden');
                
                if (data.success) {
                    displaySummary(data.summary);
                    allRecords = data.data;
                    displayResults();
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                loading.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
                console.error('Error:', error);
            });
    }

    // Display summary cards
    function displaySummary(summary) {
        summaryCards.innerHTML = `
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Distributors</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">${summary.total_distributors}</dd>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Orders</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">${summary.total_orders}</dd>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Commission</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">$${formatNumber(summary.total_commission)}</dd>
                </div>
            </div>
        `;
    }

    // Display results in table
    function displayResults() {
        const totalRecords = allRecords.length;
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        const pageRecords = allRecords.slice(startIndex, endIndex);
        
        resultsCount.textContent = `Showing ${totalRecords} distributors`;
        
        if (totalRecords === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No distributors found</td></tr>';
            document.getElementById('pagination').classList.add('hidden');
            return;
        }

        document.getElementById('pagination').classList.remove('hidden');

        tableBody.innerHTML = pageRecords.map((record, index) => {
            return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-bold text-gray-900 dark:text-white">${record.rank}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">${record.distributor_name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600 dark:text-green-400">
                        $${formatNumber(record.total_order_volume)}
                    </td>
                </tr>
            `;
        }).join('');
        
        updatePagination();
    }

    // Update pagination controls
    function updatePagination() {
        const totalRecords = allRecords.length;
        const totalPages = Math.ceil(totalRecords / recordsPerPage);
        const startRecord = totalRecords > 0 ? (currentPage - 1) * recordsPerPage + 1 : 0;
        const endRecord = Math.min(currentPage * recordsPerPage, totalRecords);
        
        // Update pagination info
        document.getElementById('paginationInfo').innerHTML = `
            Showing <span class="font-medium">${startRecord}</span> to <span class="font-medium">${endRecord}</span> of <span class="font-medium">${totalRecords}</span> results
        `;
        
        // Generate pagination buttons
        const paginationButtons = document.getElementById('paginationButtons');
        let buttonsHTML = '';
        
        // Previous button
        buttonsHTML += `
            <button onclick="changePage(${currentPage - 1})" 
                    ${currentPage === 1 ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-1 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                Previous
            </button>
        `;
        
        // Page numbers
        const maxButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxButtons - 1);
        
        if (endPage - startPage < maxButtons - 1) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            buttonsHTML += `
                <button onclick="changePage(${i})" 
                        class="relative inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 ${currentPage === i ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-500 text-indigo-600 dark:text-indigo-400 z-10' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600'} text-xs font-medium">
                    ${i}
                </button>
            `;
        }
        
        // Next button
        buttonsHTML += `
            <button onclick="changePage(${currentPage + 1})" 
                    ${currentPage === totalPages ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-1 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}">
                Next
            </button>
        `;
        
        paginationButtons.innerHTML = buttonsHTML;
        
        // Mobile pagination buttons
        const prevMobile = document.getElementById('prevMobile');
        const nextMobile = document.getElementById('nextMobile');
        
        prevMobile.disabled = currentPage === 1;
        nextMobile.disabled = currentPage === totalPages;
        
        prevMobile.onclick = () => changePage(currentPage - 1);
        nextMobile.onclick = () => changePage(currentPage + 1);
        
        if (currentPage === 1) {
            prevMobile.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            prevMobile.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        if (currentPage === totalPages) {
            nextMobile.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            nextMobile.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Change page function
    window.changePage = function(page) {
        const totalPages = Math.ceil(allRecords.length / recordsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        displayResults();
    };

    // Helper function
    function formatNumber(num) {
        return parseFloat(num).toFixed(2);
    }
});
</script>
@endsection
