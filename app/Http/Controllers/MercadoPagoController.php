<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use function env;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        // Configura las credenciales de Mercado Pago al inicializar el controlador
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
    }

    public function initPayment(Request $request)
    {
        // --- 1. Obtener los datos del carrito/pedido ---
        // Adapta esta sección para obtener los productos y el total de tu carrito
        // de tu base de datos o sesión. Este es un ejemplo simplificado.
        $cartItems = [
            [
                'title' => 'Producto de Ejemplo 1',
                'quantity' => 1,
                'unit_price' => 10000, // Precio unitario
                'description' => 'Descripción del Producto 1',
                'currency_id' => 'CLP' // Moneda: CLP para pesos chilenos
            ],
            [
                'title' => 'Producto de Ejemplo 2',
                'quantity' => 2,
                'unit_price' => 5000, // Precio unitario
                'description' => 'Descripción del Producto 2',
                'currency_id' => 'CLP'
            ]
        ];

        // Calcular el total
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['quantity'] * $item['unit_price'];
        }

        $orderId = uniqid('order-'); // Genera un ID único para tu pedido

        // --- 2. Crear una preferencia de pago en Mercado Pago ---
        $client = new PreferenceClient();
        
        $request = [
            "external_reference" => $orderId, // Tu ID de pedido para asociarlo
            "items" => $cartItems,
            // --- 3. Configurar URLs de retorno y notificaciones (Webhooks) ---
            // Estas URLs son a donde Mercado Pago redirigirá al usuario y enviará notificaciones
            "back_urls" => [
                "success" => route('mercadopago.success'),
                "pending" => route('mercadopago.pending'),
                "failure" => route('mercadopago.failure'),
            ],
            "auto_return" => "approved", // Redirige automáticamente al usuario si el pago es aprobado
            // URL para notificaciones IPN (clave para actualizar estados en el backend de forma segura)
            // DEBES USAR UNA URL PÚBLICA AQUÍ (ej. ngrok si estás en local, o tu dominio si ya está desplegado)
            "notification_url" => route('mercadopago.webhook'),
        ];

        try {
            $preference = $client->create($request); // Envía la preferencia a la API de Mercado Pago

            // 4. Redirigir al usuario a la URL de pago de Mercado Pago
            // Usa 'sandbox_init_point' para pruebas y 'init_point' para producción
            $paymentUrl = env('MERCADOPAGO_ENV') === 'sandbox' ? $preference->sandbox_init_point : $preference->init_point;

            return redirect()->away($paymentUrl);

        } catch (MPApiException $e) {
            // Manejar errores si la creación de la preferencia falla
            return redirect()->back()->with('error', 'Error al iniciar el pago con Mercado Pago: ' . $e->getMessage());
        }
    }

    // --- Métodos para manejar los retornos del usuario desde Mercado Pago ---
    // (Estos son menos fiables que los webhooks para actualizar el estado del pedido)
    public function paymentSuccess(Request $request)
    {
        // Log INMEDIATO para verificar que llega la petición
        Log::info('🎉 PAYMENT SUCCESS ROUTE ACCESSED!', [
            'timestamp' => now(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $collectionStatus = $request->input('collection_status'); // 'approved', 'pending', 'rejected'
        $externalReference = $request->input('external_reference'); // Tu orderId
        $orderRef = $request->input('order_ref'); // Nuestro parámetro personalizado
        $paymentId = $request->input('payment_id'); // ID del pago de MercadoPago

        Log::info('MercadoPago Payment Success Callback', [
            'collection_status' => $collectionStatus,
            'external_reference' => $externalReference,
            'order_ref' => $orderRef,
            'payment_id' => $paymentId,
            'all_params' => $request->all()
        ]);

        if ($collectionStatus === 'approved') {
            // Buscar y actualizar el pedido si existe
            $referenceToSearch = $externalReference ?: $orderRef;
            
            Log::info('Searching for order with reference: ' . $referenceToSearch);
            
            if ($referenceToSearch) {
                try {
                    $order = \App\Models\Order::where('reference_id', $referenceToSearch)->first();
                    
                    Log::info('Order search result', [
                        'order_found' => $order ? 'YES' : 'NO',
                        'order_id' => $order->id ?? 'N/A',
                        'order_status' => $order->status ?? 'N/A'
                    ]);
                    
                    if ($order && $order->status === 'pending') {
                        $order->update([
                            'status' => 'completed',
                            'mp_payment_id' => $paymentId ?? null
                        ]);
                        Log::info('✅ Order updated to completed', ['order_id' => $order->id, 'reference' => $referenceToSearch]);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Error updating order status: ' . $e->getMessage());
                }
            }

            // Mostrar mensaje de éxito al usuario
            Log::info('Showing success view with reference: ' . $referenceToSearch);
            return view('mercadopago.success', ['externalReference' => $referenceToSearch]);
        }
        
        Log::warning('Payment not approved, redirecting to dashboard', ['collection_status' => $collectionStatus]);
        return redirect()->route('buyer.dashboard')->with('error', 'Error inesperado en el retorno de pago.');
    }

    public function paymentPending(Request $request)
    {
        $externalReference = $request->input('external_reference');
        // Mostrar mensaje de pago pendiente al usuario
        return view('mercadopago.pending', compact('externalReference'));
    }

    public function paymentFailure(Request $request)
    {
        $externalReference = $request->input('external_reference');
        // Mostrar mensaje de pago rechazado al usuario
        return view('mercadopago.failure', compact('externalReference'));
    }

    // --- Método para manejar las notificaciones IPN (Webhooks) de Mercado Pago ---
    // ESTE ES CRÍTICO PARA ACTUALIZAR EL ESTADO REAL DEL PEDIDO EN TU DB
    public function handleWebhook(Request $request)
    {
        Log::info('🔔 MercadoPago Webhook received', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        // Mercado Pago enviará notificaciones aquí (payload JSON o parámetros URL)
        $topic = $request->input('topic');
        $id = $request->input('id'); // ID de la notificación
        $orderRef = $request->input('order_ref'); // Nuestro parámetro personalizado

        Log::info('Webhook details', [
            'topic' => $topic,
            'id' => $id,
            'order_ref' => $orderRef
        ]);

        if ($topic === 'payment' && $id) {
            try {
                // Obtener los detalles completos del pago desde la API de Mercado Pago usando SDK v3
                $client = new PaymentClient();
                $payment = $client->get($id);

                if ($payment) {
                    $externalReference = $payment->external_reference; // Tu ID de pedido
                    $status = $payment->status; // 'approved', 'pending', 'rejected'

                    Log::info('Payment webhook details', [
                        'payment_id' => $id,
                        'external_reference' => $externalReference,
                        'status' => $status,
                        'order_ref_param' => $orderRef
                    ]);

                    // Buscar el pedido usando external_reference o order_ref
                    $referenceToSearch = $externalReference ?: $orderRef;
                    
                    if ($referenceToSearch) {
                        $order = \App\Models\Order::where('reference_id', $referenceToSearch)->first();
                        
                        if ($order) {
                            $newStatus = match($status) {
                                'approved' => 'completed',
                                'pending' => 'pending',
                                'rejected' => 'cancelled',
                                default => 'pending'
                            };

                            $order->update([
                                'status' => $newStatus,
                                'mp_payment_id' => $id
                            ]);

                            Log::info('✅ Order updated via webhook', [
                                'order_id' => $order->id,
                                'old_status' => $order->getOriginal('status'),
                                'new_status' => $newStatus,
                                'payment_id' => $id
                            ]);
                        } else {
                            Log::warning('❌ Order not found for webhook', ['reference' => $referenceToSearch]);
                        }
                    }
                }

                return response('OK', 200);
                
            } catch (\Exception $e) {
                Log::error('Error processing payment webhook: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'payment_id' => $id
                ]);
                
                return response('ERROR', 500);
            }
        }

        // Para otros tipos de notificaciones o si no hay ID
        Log::info('Webhook received but not processed', ['topic' => $topic, 'id' => $id]);
        return response('OK', 200);
    }

    private function transferToSeller(Order $order)
    {
        $sellerAmount = $order->total * 0.985; // 98.5% para el vendedor
        $platformFee = $order->total * 0.015; // 1.5% para la plataforma

        // Aquí puedes implementar la lógica para realizar la transferencia al vendedor
        Log::info('Transferencia realizada al vendedor', [
            'seller_id' => $order->seller_id,
            'amount' => $sellerAmount,
            'platform_fee' => $platformFee,
        ]);
    }
}