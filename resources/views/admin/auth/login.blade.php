<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('/uploads/background-admin.avif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    
    <!-- Container com fundo amarelo claro -->
    <div class="w-full max-w-xs bg-amber-50 p-8 rounded-lg shadow-lg">
        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
            @csrf
            
            <input 
                type="password" 
                name="access_token" 
                id="access_token"
                class="w-full px-4 py-3 bg-white border border-amber-200 rounded focus:outline-none focus:border-amber-300 text-gray-700"
                required
                autofocus
                autocomplete="off"
            >
            
            @error('access_token')
                <div class="text-orange-600 text-sm">{{ $message }}</div>
            @enderror

            <button 
                type="submit"
                class="w-full py-3 bg-amber-200 hover:bg-amber-300 rounded transition-colors text-gray-700 font-medium"
            >
                𓅱𓈖 𓂋𓈖
            </button>
        </form>
    </div>

</body>
</html>
