<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if($pixelEnabled && $pixelId)
    <!-- Facebook Pixel Code -->
    <script>
      const TRACKING_DATA = @json($trackingData);

      function trackingPayload(base = {}) {
        if (!TRACKING_DATA) {
          return base;
        }

        const extras = {};
        const keys = ['utm_source','utm_medium','utm_campaign','utm_content','utm_term','utm_id','fbclid','gclid','wbraid','gbraid'];

        keys.forEach(key => {
          if (TRACKING_DATA[key]) {
            extras[key] = TRACKING_DATA[key];
          }
        });

        if (TRACKING_DATA.landing_page) {
          extras.landing_page = TRACKING_DATA.landing_page;
        }

        if (TRACKING_DATA.referrer) {
          extras.referrer = TRACKING_DATA.referrer;
        }

        return Object.assign({}, base, extras);
      }

      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      
      fbq('init', '{{ $pixelId }}');
      fbq('track', 'PageView');
      
      // ViewContent - Produto visualizado
      fbq('track', 'ViewContent', trackingPayload({
        content_name: '{{ $oferta["nome"] }}',
        content_ids: ['{{ $oferta["id"] }}'],
        content_type: 'product',
        value: {{ json_encode($ofertaPrecoFloat) }},
        currency: 'USD'
      }));
    </script>
    <noscript>
      <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif
</head>
<body class="bg-gray-50">
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" style="display:none;" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-700">Processing payment...</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $oferta['nome'] }}</h1>
            <p class="text-gray-600 mt-2">{{ $oferta['descricao'] }}</p>
            <p class="text-4xl font-bold text-blue-600 mt-4">${{ number_format($ofertaPrecoFloat, 2, ',', '.') }}</p>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            
            <!-- Mensagem de erro -->
            <div id="payment-error-message" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-red-800 mb-1">Payment Error</h3>
                        <p id="payment-error-text" class="text-sm text-red-700"></p>
                    </div>
                    <button type="button" onclick="hidePaymentError()" class="text-red-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="payment-form" class="space-y-6">
                
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="John Doe"
                        required
                    >
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="john@example.com"
                        required
                    >
                </div>

                <!-- Card Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                    <input 
                        type="text" 
                        id="card-number" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="1234 5678 9012 3456"
                        maxlength="19"
                        required
                    >
                </div>

                <!-- Expiry and CVV -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Month</label>
                        <select id="card-month" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Month</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Year</label>
                        <select id="card-year" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Year</option>
                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- CVV -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                    <input 
                        type="text" 
                        id="card-cvv" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="123"
                        maxlength="4"
                        required
                    >
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
                >
                    Complete Purchase - ${{ number_format($ofertaPrecoFloat, 2, '.', ',') }}
                </button>
            </form>

            <!-- Security Badge -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">🔒 Secure SSL encrypted payment</p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('payment-form');
        const loadingOverlay = document.getElementById('loading-overlay');
        let initiateCheckoutFired = false;
        let addPaymentInfoFired = false;
        const OFFER_PRICE = {{ json_encode($ofertaPrecoFloat) }};

        // InitiateCheckout - quando usuário começa a preencher
        document.getElementById('name').addEventListener('focus', function() {
            if (!initiateCheckoutFired && typeof fbq !== 'undefined') {
                fbq('track', 'InitiateCheckout', trackingPayload({
                    value: OFFER_PRICE,
                    currency: 'USD',
                    content_ids: ['{{ $oferta["id"] }}']
                }));
                initiateCheckoutFired = true;
            }
        }, { once: true });

        // AddPaymentInfo - quando começa a digitar o cartão
        document.getElementById('card-number').addEventListener('input', function() {
            if (!addPaymentInfoFired && typeof fbq !== 'undefined') {
                fbq('track', 'AddPaymentInfo', trackingPayload({
                    value: OFFER_PRICE,
                    currency: 'USD'
                }));
                addPaymentInfoFired = true;
            }
        }, { once: true });

        // Submit do formulário
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            hidePaymentError();

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                cardNumber: document.getElementById('card-number').value.replace(/\s/g, ''),
                cardMonth: document.getElementById('card-month').value,
                cardYear: document.getElementById('card-year').value,
                cardCvv: document.getElementById('card-cvv').value
            };

            loadingOverlay.style.display = 'flex';

            fetch('{{ route("pay.createOrder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                } else {
                    loadingOverlay.style.display = 'none';
                    showPaymentError(data.message || 'Payment failed. Please try again.');
                    
                    // Tracking: Payment Declined
                    if (typeof fbq !== 'undefined') {
                        fbq('trackCustom', 'PaymentDeclined', trackingPayload({
                            reason: data.message,
                            value: OFFER_PRICE,
                            currency: 'USD'
                        }));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingOverlay.style.display = 'none';
                showPaymentError('An error occurred. Please try again.');
            });
        });

        function showPaymentError(message) {
            const errorDiv = document.getElementById('payment-error-message');
            const errorText = document.getElementById('payment-error-text');
            errorText.textContent = message;
            errorDiv.classList.remove('hidden');
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hidePaymentError() {
            document.getElementById('payment-error-message').classList.add('hidden');
        }

        // Format card number
        document.getElementById('card-number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = value;
        });
    </script>
</body>
</html>

