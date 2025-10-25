@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Client Database')
@section('page-description', 'Add and manage your clients to better understand preferences and sources')

@section('content')

    <!-- Client Form -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-neutral-200 mb-10">
        <h3 class="text-xl font-semibold mb-6">Add New Client</h3>

        <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Client Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Client Type</label>
                    <select name="client_type" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                        <option value="individual">Individual</option>
                        <option value="company">Company</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Status</label>
                    <select name="status" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                        <option value="lead">Lead</option>
                        <option value="prospect">Prospect</option>
                        <option value="customer">Customer</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>

                <div id="company_name_field" style="display: none;">
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Company Name</label>
                    <input type="text" name="company_name"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>

                <div id="contact_person_field" style="display: none;">
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Primary Contact *</label>
                    <input type="text" name="contact" required
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"
                        placeholder="Phone or Email">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Phone</label>
                    <input type="tel" name="phone"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>
            </div>

            <!-- Address Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">City</label>
                    <input type="text" name="city"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Postal Code</label>
                    <input type="text" name="postal_code"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Country</label>
                    <input type="text" name="country" value="Morocco"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"></textarea>
            </div>

            <!-- Business Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">How They Heard About Us</label>
                    <select name="source"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                        <option value="Instagram">Instagram</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Friend / Recommendation">Friend / Recommendation</option>
                        <option value="Architect / Designer">Architect / Designer</option>
                        <option value="Store Visit">Store Visit</option>
                        <option value="Website">Website</option>
                        <option value="Google">Google</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Budget Range (MAD)</label>
                    <input type="number" name="budget_range" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Last Contact Date</label>
                <input type="date" name="last_contact_date"
                    class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <!-- Preferences and Notes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">What They Like</label>
                    <textarea name="likes" rows="3" placeholder="Colors, styles, products they liked..."
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Additional notes about the client..."
                        class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none"></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all">
                    Add Client
                </button>
            </div>
        </form>
    </div>

    <script>
        // Show/hide company fields based on client type
        document.querySelector('select[name="client_type"]').addEventListener('change', function () {
            const companyNameField = document.getElementById('company_name_field');
            const contactPersonField = document.getElementById('contact_person_field');

            if (this.value === 'company') {
                companyNameField.style.display = 'block';
                contactPersonField.style.display = 'block';
            } else {
                companyNameField.style.display = 'none';
                contactPersonField.style.display = 'none';
            }
        });
    </script>

    <!-- Clients List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mt-10 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Clients</h3>
            <p class="text-sm text-gray-500">Latest clients added to your database</p>
        </div>

        @if($clients->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($clients as $client)
                    <a href="{{ route('clients.show', $client) }}"
                        class="block bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow hover:bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $client->display_name }}</h4>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $client->client_type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($client->client_type) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Status:
                            <span
                                class="font-medium {{ $client->status === 'customer' ? 'text-green-800' : ($client->status === 'prospect' ? 'text-yellow-800' : ($client->status === 'lead' ? 'text-blue-800' : 'text-gray-600')) }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </p>
                        <p class="text-sm text-gray-500 mb-1">Rep: {{ $client->user->name }}</p>
                        @if($client->city)
                            <p class="text-sm text-gray-500">City: {{ $client->city }}</p>
                        @endif
                        @if($client->source)
                            <p class="text-sm text-gray-500 mt-1">Source: {{ $client->source }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No clients found</h3>
                <p class="mt-1 text-sm text-gray-500">Start by adding new clients using the form above.</p>
            </div>
        @endif
    </div>

@endsection