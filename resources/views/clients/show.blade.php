@extends('layouts.app')

@section('title', 'Client Details')
@section('page-title', 'Client Details')
@section('page-description', 'View detailed information and invoices for this client')

@section('header-actions')
    <div class="flex items-center space-x-3">
        <a href="{{ route('clients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Clients
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Client Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $client->display_name }}</h3>
                            <p class="text-sm text-gray-500">Client Information</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $client->client_type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($client->client_type) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $client->status === 'customer' ? 'bg-green-100 text-green-800' : 
                                   ($client->status === 'prospect' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($client->status === 'lead' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Contact Information</h4>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Contact Person:</span>
                                    <p class="text-gray-600">{{ $client->contact }}</p>
                                </div>
                                @if($client->phone)
                                    <div>
                                        <span class="font-medium text-gray-700">Phone:</span>
                                        <p class="text-gray-600">{{ $client->phone }}</p>
                                    </div>
                                @endif
                                @if($client->email)
                                    <div>
                                        <span class="font-medium text-gray-700">Email:</span>
                                        <p class="text-gray-600">{{ $client->email }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Location</h4>
                            <div class="space-y-3 text-sm">
                                @if($client->city)
                                    <div>
                                        <span class="font-medium text-gray-700">City:</span>
                                        <p class="text-gray-600">{{ $client->city }}</p>
                                    </div>
                                @endif
                                @if($client->address)
                                    <div>
                                        <span class="font-medium text-gray-700">Address:</span>
                                        <p class="text-gray-600">{{ $client->address }}</p>
                                    </div>
                                @endif
                                @if($client->postal_code)
                                    <div>
                                        <span class="font-medium text-gray-700">Postal Code:</span>
                                        <p class="text-gray-600">{{ $client->postal_code }}</p>
                                    </div>
                                @endif
                                @if($client->country)
                                    <div>
                                        <span class="font-medium text-gray-700">Country:</span>
                                        <p class="text-gray-600">{{ $client->country }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Business Details</h4>
                            <div class="space-y-3 text-sm">
                                @if($client->company_name)
                                    <div>
                                        <span class="font-medium text-gray-700">Company:</span>
                                        <p class="text-gray-600">{{ $client->company_name }}</p>
                                    </div>
                                @endif
                                @if($client->contact_person)
                                    <div>
                                        <span class="font-medium text-gray-700">Contact Person:</span>
                                        <p class="text-gray-600">{{ $client->contact_person }}</p>
                                    </div>
                                @endif
                                @if($client->source)
                                    <div>
                                        <span class="font-medium text-gray-700">Source:</span>
                                        <p class="text-gray-600">{{ $client->source }}</p>
                                    </div>
                                @endif
                                @if($client->budget_range)
                                    <div>
                                        <span class="font-medium text-gray-700">Budget:</span>
                                        <p class="text-gray-600">{{ number_format($client->budget_range, 0) }} MAD</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900">Additional Information</h4>
                            <div class="space-y-3 text-sm">
                                @if($client->likes)
                                    <div>
                                        <span class="font-medium text-gray-700">Preferences:</span>
                                        <p class="text-gray-600">{{ $client->likes }}</p>
                                    </div>
                                @endif
                                @if($client->notes)
                                    <div>
                                        <span class="font-medium text-gray-700">Notes:</span>
                                        <p class="text-gray-600">{{ $client->notes }}</p>
                                    </div>
                                @endif
                                @if($client->last_contact_date)
                                    <div>
                                        <span class="font-medium text-gray-700">Last Contact:</span>
                                        <p class="text-gray-600">{{ \Carbon\Carbon::parse($client->last_contact_date)->format('M d, Y') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="font-medium text-gray-700">Added By:</span>
                                    <p class="text-gray-600">{{ $client->user->name }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Created:</span>
                                    <p class="text-gray-600">{{ $client->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Invoices -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Client Invoices</h3>
                    <p class="text-sm text-gray-500">All invoices for this client</p>
                </div>
                <div class="p-6">
                    @if($invoices->count() > 0)
                        <div class="space-y-4">
                            @foreach($invoices as $invoice)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h4 class="font-semibold text-gray-900">{{ $invoice->invoice_number }}</h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                                       ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : 
                                                       ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                                       ($invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800'))) }}">
                                                    {{ ucfirst($invoice->status) }}
                                                </span>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-gray-700">Date:</span>
                                                    <p class="text-gray-600">{{ $invoice->invoice_date->format('M d, Y') }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-700">Amount:</span>
                                                    <p class="text-gray-600 font-semibold">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-700">Created By:</span>
                                                    <p class="text-gray-600">{{ $invoice->user->name }}</p>
                                                </div>
                                            </div>
                                            
                                            @if($invoice->description)
                                                <div class="mt-3">
                                                    <span class="font-medium text-gray-700">Description:</span>
                                                    <p class="text-gray-600 text-sm">{{ $invoice->description }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View Invoice">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
                            <p class="mt-1 text-sm text-gray-500">This client doesn't have any invoices yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Invoice
                    </a>
                    
                    <a href="{{ route('clients.index') }}" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Clients
                    </a>
                </div>
            </div>

            <!-- Client Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Client Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Invoices</span>
                        <span class="font-semibold text-gray-900">{{ $invoices->count() }}</span>
                    </div>
                    
                    @if($invoices->count() > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Amount</span>
                            <span class="font-semibold text-gray-900">
                                {{ number_format($invoices->sum('amount'), 2) }} MAD
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Paid Invoices</span>
                            <span class="font-semibold text-green-600">
                                {{ $invoices->where('status', 'paid')->count() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending Invoices</span>
                            <span class="font-semibold text-yellow-600">
                                {{ $invoices->whereIn('status', ['sent', 'draft'])->count() }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
