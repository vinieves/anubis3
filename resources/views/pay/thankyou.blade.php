<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if($pixelEnabled && $pixelId)
    <!-- Facebook Pixel Code -->
    <script>
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
      
      @if(session('pay_conversion_data'))
      // PURCHASE EVENT - CONVERSÃO!
      fbq('track', 'Purchase', {
        value: {{ session('pay_conversion_data.value') }},
        currency: '{{ session('pay_conversion_data.currency') }}',
        transaction_id: '{{ session('pay_conversion_data.transaction_id') }}',
        content_ids: @json(session('pay_conversion_data.content_ids')),
        content_name: '{{ session('pay_conversion_data.content_name') }}'
      });
      @php session()->forget('pay_conversion_data'); @endphp
      @endif
    </script>
    @endif
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-2xl p-8 text-center">
        
        <!-- Success Icon -->
        <div class="mb-6">
            <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <!-- Thank You Message -->
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Thank You!</h1>
        <p class="text-xl text-gray-600 mb-8">Your payment has been processed successfully.</p>

        <!-- Order Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Confirmation</h2>
            <div class="space-y-2 text-left">
                @if(session('pay_conversion_data.transaction_id'))
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-medium">{{ session('pay_conversion_data.transaction_id') }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium text-green-600">✓ Approved</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium">{{ session('pay_customer_email', 'N/A') }}</span>
                </div>
            </div>
        </div>

        <!-- Check Email -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <p class="text-blue-800">
                📧 A confirmation email has been sent to your inbox.
            </p>
        </div>

        <!-- CTA -->
        <p class="text-gray-500 text-sm">
            You can close this page now.
        </p>
    </div>

</body>
</html>

