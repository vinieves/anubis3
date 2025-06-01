<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upsell 2</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .payment-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 16px;
        }

        .payment-text {
            font-size: 16px;
            color: #1a1a1a;
            margin-bottom: 24px;
            line-height: 1.4;
        }

        .button {
            background-color: #1e293b;
            color: #d4a853;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s;
            font-weight: 500;
        }

        .button:hover {
            background-color: #2d3446;
        }

        #loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.95);
            justify-content: center;
            align-items: center;
            z-index: 50;
            text-align: center;
        }

        .loading-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #1a1f36;
            border-top: 4px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h2>Accessing your product</h2>
            <p>Please wait while your product is loaded...</p>
        </div>
    </div>

    <div class="container">
        <svg class="payment-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" stroke="#22c55e"/>
            <path d="M8 12l3 3 5-5" stroke="#22c55e"/>
        </svg>
        <p class="payment-text">Your order is complete,<br>to proceed you need to click<br>Access below.</p>
        <button class="button" id="continueButton">Click to access</button>
    </div>

    <script>
        // Debug do CSRF token
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        document.getElementById('continueButton').addEventListener('click', function() {
            const loadingOverlay = document.getElementById('loading-overlay');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            console.log('Iniciando requisição com CSRF token:', csrfToken);
            loadingOverlay.style.display = 'flex';

            fetch('/upsell2/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Status da resposta:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Resposta do servidor:', data);
                loadingOverlay.style.display = 'none';
                if (data.success) {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.payment_status || 'Pagamento processado com sucesso!');
                        window.location.href = '/';
                    }
                } else {
                    alert(data.message || 'Ocorreu um erro ao processar o pagamento');
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                loadingOverlay.style.display = 'none';
                //alert('Erro ao processar o pagamento: ' + error.message);
            });
        });
    </script>
</body>
</html> 