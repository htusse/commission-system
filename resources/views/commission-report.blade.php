@extends('layouts.app')

@section('title', 'Commission Report')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Commission Report</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and filter commission records for all orders</p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <form id="filterForm" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="distributor_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Distributor</label>
                <input type="text" 
                       id="distributor_name" 
                       name="distributor_name"
                       placeholder="Enter distributor name..."
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date From</label>
                <input type="date" 
                       id="date_from" 
                       name="date_from"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date To</label>
                <input type="date" 
                       id="date_to" 
                       name="date_to"
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="invoice_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice</label>
                <input type="text" 
                       id="invoice_number" 
                       name="invoice_number"
                       placeholder="Enter invoice number..."
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            
            <div class="sm:col-span-2 lg:col-span-3 flex gap-2">
                <button type="submit" 
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Filter
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
    <div id="loading" class="text-center py-12">
        <svg class="animate-spin h-12 w-12 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Loading...</p>
    </div>

    <!-- Results Table -->
    <div id="resultsContainer" class="hidden bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div id="resultsCount" class="mb-4 text-sm text-gray-500 dark:text-gray-400"></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Invoice</th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Purchaser</th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Distributor</th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Referred</th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                            <th scope="col" class="px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">%</th>
                            <th scope="col" class="px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                            <th scope="col" class="px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Commission</th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
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

<!-- Invoice Items Modal -->
<div id="orderModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('orderModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                        Invoice: <span id="modalInvoiceNumber"></span>
                    </h3>
                    <button type="button" 
                            id="closeModalX"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4" id="orderDetails">
                    <!-- Order details will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for modal access
let modal, orderDetails, modalInvoiceNumber;

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const clearBtn = document.getElementById('clearFilters');
    const loading = document.getElementById('loading');
    const resultsContainer = document.getElementById('resultsContainer');
    const resultsCount = document.getElementById('resultsCount');
    const tableBody = document.getElementById('tableBody');
    modal = document.getElementById('orderModal');
    const closeModalX = document.getElementById('closeModalX');
    orderDetails = document.getElementById('orderDetails');
    modalInvoiceNumber = document.getElementById('modalInvoiceNumber');
    
    // Pagination variables
    let allRecords = [];
    let currentPage = 1;
    const recordsPerPage = 5;

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

    // Close modal
    closeModalX.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    });

    // Load commission data
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

        fetch(`/api/v1/commission-report?${params}`)
            .then(response => response.json())
            .then(data => {
                loading.classList.add('hidden');
                resultsContainer.classList.remove('hidden');
                
                if (data.success) {
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

    // Display results in table
    function displayResults() {
        const totalRecords = allRecords.length;
        const startIndex = (currentPage - 1) * recordsPerPage;
        const endIndex = Math.min(startIndex + recordsPerPage, totalRecords);
        const pageRecords = allRecords.slice(startIndex, endIndex);
        
        resultsCount.textContent = `Showing ${totalRecords} records`;
        
        if (totalRecords === 0) {
            tableBody.innerHTML = '<tr><td colspan="9" class="px-2 py-3 text-center text-gray-500 dark:text-gray-400 text-xs">No records found</td></tr>';
            document.getElementById('pagination').classList.add('hidden');
            return;
        }

        document.getElementById('pagination').classList.remove('hidden');

        tableBody.innerHTML = pageRecords.map(record => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-2 py-2 whitespace-nowrap text-xs font-medium text-gray-900 dark:text-white">
                    ${record.invoice_number}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">
                    ${record.purchaser_name || 'N/A'}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-900 dark:text-white">
                    ${record.referrer_name || 'No Referrer'}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-center text-gray-500 dark:text-gray-400">
                    ${record.referred_distributors}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                    ${formatDate(record.order_date)}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-right text-gray-500 dark:text-gray-400">
                    ${(record.commission_rate * 100).toFixed(0)}%
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-right text-gray-900 dark:text-white">
                    $${formatNumber(record.order_total)}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-right font-medium ${record.commission_amount > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'}">
                    $${formatNumber(record.commission_amount)}
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-xs text-center">
                    <button onclick="viewOrder(${record.order_id})" 
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">
                        View
                    </button>
                </td>
            </tr>
        `).join('');
        
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
    
    // Change page
    window.changePage = function(page) {
        const totalPages = Math.ceil(allRecords.length / recordsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        displayResults();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Helper functions
    function formatDate(dateString) {
        const date = new Date(dateString);
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = date.getFullYear();
        return `${month}/${day}/${year}`;
    }

    function formatNumber(num) {
        return parseFloat(num).toFixed(2);
    }

    // View order details - global function
    window.viewOrder = function(orderId) {
        // Show loading state in modal while fetching
        modalInvoiceNumber.textContent = 'Loading...';
        orderDetails.innerHTML = '<div class="text-center py-4"><p class="text-gray-500">Loading order details...</p></div>';
        modal.classList.remove('hidden');
        
        fetch(`/api/v1/commission-report/order/${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.data.order;
                    const items = data.data.items;

                    modalInvoiceNumber.textContent = order.invoice_number;

                    orderDetails.innerHTML = `
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-indigo-100 dark:bg-indigo-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">SKU</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Product Name</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Price</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Quantity</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    ${items.map(item => `
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${item.product_sku}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${item.product_name}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-900 dark:text-white">${formatNumber(item.price)}</td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">${item.quantity}</td>
                                            <td class="px-4 py-3 text-sm text-center font-medium text-gray-900 dark:text-white">${formatNumber(item.subtotal)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    orderDetails.innerHTML = '<div class="text-center py-4"><p class="text-red-500">Failed to load order details.</p></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                orderDetails.innerHTML = '<div class="text-center py-4"><p class="text-red-500">Error loading order details.</p></div>';
            });
    };
});
</script>
@endsection
