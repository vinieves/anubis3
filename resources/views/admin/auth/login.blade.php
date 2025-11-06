<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 360px;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 15px;
            transition: all 0.2s;
            background: #ffffff;
            color: #111827;
        }
        input[type="password"]:focus {
            outline: none;
            border-color: #111827;
        }
        input[type="password"]::placeholder {
            color: #9ca3af;
        }
        button {
            width: 100%;
            padding: 14px 16px;
            background: #111827;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        button:hover {
            background: #1f2937;
        }
        button:active {
            transform: scale(0.98);
        }
        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            
            <div class="form-group">
                <input 
                    type="password" 
                    name="access_token" 
                    id="access_token"
                    placeholder="Access Token"
                    value="{{ old('access_token') }}"
                    required
                    autofocus
                    autocomplete="off"
                >
                @error('access_token')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>

