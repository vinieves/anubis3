<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-amber-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-xs">
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
                class="w-full py-3 bg-amber-200 hover:bg-amber-300 rounded transition-colors"
            ></button>
        </form>
    </div>

</body>
</html>
