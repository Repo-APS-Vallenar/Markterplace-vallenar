@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-green-600 mb-2">¡Pago Exitoso!</h1>
            <p class="text-gray-600">Tu pago ha sido procesado correctamente</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">Número de pedido:</p>
            <p class="font-mono text-gray-800">{{ $externalReference ?? 'N/A' }}</p>
        </div>

        <div class="space-y-3">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-blue-800 text-sm">
                    <strong>✅ Compra completada exitosamente</strong><br>
                    Tu pago fue procesado correctamente en MercadoPago.<br>
                    <strong>Número de operación:</strong> #1339372479
                </p>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-yellow-800 text-sm">
                    <strong>📧 Próximos pasos:</strong><br>
                    • Recibirás un email de confirmación<br>
                    • El vendedor te contactará para coordinar la entrega<br>
                    • Puedes revisar tu pedido en "Mis Pedidos"
                </p>
            </div>
            
            <a href="{{ route('buyer.index') }}" 
               class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200 inline-block font-semibold text-lg">
                🏠 Volver al Marketplace
            </a>
            
            <a href="{{ route('buyer.orders.index') }}" 
               class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 inline-block">
                📋 Ver mis pedidos
            </a>
            
            <a href="{{ route('buyer.index') }}" 
               class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 inline-block">
                🛍️ Seguir comprando
            </a>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>Recibirás un email de confirmación pronto.</p>
            <p>Si tienes dudas, contacta al vendedor.</p>
            <p class="mt-2 text-blue-500">Serás redirigido automáticamente en <span id="countdown">5</span> segundos...</p>
        </div>
    </div>
</div>

<script>
    // Redirección automática después de 5 segundos
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = "{{ route('buyer.index') }}";
        }
    }, 1000);
</script>
@endsection