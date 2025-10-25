@extends('layouts.app')

@section('title', 'Invoice Management')
@section('page-title', 'Invoice Management')
@section('page-description', 'Track and manage client invoices and payments')

@section('header-actions')
    <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Invoice
    </a>
@endsection

@section('content')
    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Invoices List</h3>
            <p class="text-sm text-gray-500">Manage all your invoices in one place</p>
        </div>
        
        <div class="p-6">
            @if($invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b border-gray-200">
                                <th class="pb-3 font-medium">Invoice #</th>
                                <th class="pb-3 font-medium">Client</th>
                                <th class="pb-3 font-medium">Date</th>
                                <th class="pb-3 font-medium">Amount</th>
                                <th class="pb-3 font-medium">Status</th>
                                <th class="pb-3 font-medium">Added By</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($invoices as $invoice)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                                    </td>
                                    <td class="py-4">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $invoice->client->display_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $invoice->client->city ?? 'No city' }}</div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-gray-700">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                    <td class="py-4">
                                        <div class="font-medium text-gray-900">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</div>
                                    </td>
                                    <td class="py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                               ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : 
                                               ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                               ($invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-gray-700">{{ $invoice->user->name }}</td>
                                    <td class="py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors" title="View Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-gray-600 hover:text-gray-800 p-1 rounded hover:bg-gray-50 transition-colors" title="Edit Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first invoice.</p>
                    <div class="mt-6">
                        <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Invoice
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection