@extends('layouts.app')

@section('title', 'All Clients')

@section('content')
    <!-- Page Title -->
    <div class="mb-6">
        <h2 class="text-3xl font-semibold mb-2">All Clients</h2>
        <p class="text-neutral-500">Browse and filter clients easily.</p>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-4 mb-6">
        <select name="status" class="px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
            <option value="">All Statuses</option>
            <option value="lead" {{ request('status')=='lead' ? 'selected' : '' }}>Lead</option>
            <option value="prospect" {{ request('status')=='prospect' ? 'selected' : '' }}>Prospect</option>
            <option value="customer" {{ request('status')=='customer' ? 'selected' : '' }}>Customer</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <select name="client_type" class="px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
            <option value="">All Types</option>
            <option value="individual" {{ request('client_type')=='individual' ? 'selected' : '' }}>Individual</option>
            <option value="company" {{ request('client_type')=='company' ? 'selected' : '' }}>Company</option>
        </select>

        <select name="source" class="px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
            <option value="">All Sources</option>
            <option value="Instagram" {{ request('source')=='Instagram' ? 'selected' : '' }}>Instagram</option>
            <option value="Facebook" {{ request('source')=='Facebook' ? 'selected' : '' }}>Facebook</option>
            <option value="Store Visit" {{ request('source')=='Store Visit' ? 'selected' : '' }}>Store Visit</option>
            <option value="Friend / Recommendation" {{ request('source')=='Friend / Recommendation' ? 'selected' : '' }}>Friend / Recommendation</option>
            <option value="Website" {{ request('source')=='Website' ? 'selected' : '' }}>Website</option>
            <option value="Google" {{ request('source')=='Google' ? 'selected' : '' }}>Google</option>
            <option value="Other" {{ request('source')=='Other' ? 'selected' : '' }}>Other</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all">Filter</button>
    </form>

    <!-- Clients Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($clients as $client)
            <a href="{{ route('clients.show', $client) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow hover:bg-gray-50">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lg font-semibold text-gray-900">{{ $client->full_name }}</h4>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $client->client_type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($client->client_type) }}
                    </span>
                </div>
                <p class="text-sm text-neutral-500 mb-1">Rep: {{ $client->user->name }}</p>
                <p class="text-sm text-neutral-500 mb-1">Status: 
                    <span class="font-medium {{ $client->status === 'customer' ? 'text-green-800' : ($client->status === 'prospect' ? 'text-yellow-800' : ($client->status === 'lead' ? 'text-blue-800' : 'text-gray-600')) }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </p>
                <p class="text-sm text-neutral-500">City: {{ $client->city ?? '-' }}</p>
                @if($client->source)
                    <p class="text-sm text-neutral-500 mt-1">Source: {{ $client->source }}</p>
                @endif
            </a>
        @empty
            <p class="text-neutral-500 col-span-full">No clients found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $clients->appends(request()->query())->links() }}
    </div>
@endsection
