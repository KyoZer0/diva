<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Diva Ceramica</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-neutral-200">
        <h2 class="text-2xl font-semibold mb-6 text-center">Create Account</h2>
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-neutral-700 mb-1">Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none" value="{{ old('name') }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-neutral-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none" value="{{ old('email') }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-neutral-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-neutral-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <button type="submit" class="w-full px-6 py-2.5 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-neutral-600">
            Already have an account? <a href="{{ route('login') }}" class="text-black hover:underline">Login</a>
        </p>
    </div>
</body>
</html>

