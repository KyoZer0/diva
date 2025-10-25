<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Diva Ceramica | Client Management System</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="bg-neutral-50 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-neutral-200">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-semibold text-neutral-900 mb-2">Diva Ceramica</h1>
                <p class="text-neutral-600">Client Management System</p>
            </div>
            
            <div class="space-y-4">
                <a href="{{ route('login') }}" class="w-full block px-6 py-3 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all text-center font-medium">
                    Login
                </a>
                
                <a href="{{ route('register') }}" class="w-full block px-6 py-3 border border-neutral-300 text-neutral-700 rounded-xl hover:bg-neutral-50 transition-all text-center font-medium">
                    Register
                </a>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-sm text-neutral-500">
                    Access your client database and manage invoices
                </p>
            </div>
        </div>
    </body>
</html>