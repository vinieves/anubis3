<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offer</title>
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
      fbq('track', 'ViewContent', {
        content_name: 'Upsell 1 - Premium Package',
        content_type: 'product'
      });
    </script>
    @endif
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-2xl w-full bg-white rounded-xl shadow-xl p-8">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Wait! Special Offer</h1>
            <p class="text-gray-600">Add this premium upgrade to your order</p>
        </div>

        <div class="mb-8 text-center">
            <p class="text-5xl font-bold text-blue-600 mb-2">$19.99</p>
            <p class="text-gray-500">One-time payment</p>
        </div>

        <div class="space-y-4 mb-8">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-gray-700">Premium Feature 1</span>
            </div>
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-gray-700">Premium Feature 2</span>
            </div>
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-gray-700">Premium Feature 3</span>
            </div>
        </div>

        <div class="space-y-4">
            <button onclick="acceptUpsell()" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                Yes! Add to My Order
            </button>
            <button onclick="declineUpsell()" class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                No, Thanks
            </button>
        </div>
    </div>

    <script>
        function acceptUpsell() {
            fetch('{{ route("pay.upsell1.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            });
        }

        function declineUpsell() {
            window.location.href = '{{ route("pay.upsell2") }}';
        }
    </script>
</body>
</html>

