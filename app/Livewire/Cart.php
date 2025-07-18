<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;


class Cart extends Component
{
    public $cart = [];
    public $payment_method = 'cash'; // Valor por defecto
    public $notes = '';
    public $showCheckoutModal = false;

    // Listener para cuando el carrito se actualiza (útil si hay otros componentes o JS)
    protected $listeners = ['cart-updated' => 'loadCartData']; // Cambiado a loadCartData para evitar conflicto con mount

    public function mount()
    {
        $this->loadCartData(); // Carga inicial del carrito
        
        // Verificar que las credenciales de MercadoPago estén configuradas
        $accessToken = env('MERCADOPAGO_ACCESS_TOKEN');
        if (empty($accessToken)) {
            Log::error('MERCADOPAGO_ACCESS_TOKEN is not set in .env file');
        } else {
            Log::info('MercadoPago Access Token is configured: ' . substr($accessToken, 0, 10) . '...');
        }
        
        // Configura las credenciales de Mercado Pago UNA SOLA VEZ, al montar el componente
        // Asegúrate de que MERCADOPAGO_ACCESS_TOKEN esté definido en tu archivo .env
        MercadoPagoConfig::setAccessToken($accessToken);
        
        // Asegurar que siempre haya un método de pago seleccionado por defecto
        if (empty($this->payment_method)) {
            $this->payment_method = 'cash';
        }
    }

    // Método para cargar/recargar los datos del carrito desde la sesión
    public function loadCartData()
    {
        $this->cart = session()->get('cart', []);
        // Si el carrito está vacío y quieres simular productos para pruebas, puedes hacerlo aquí
        /*
        if (empty($this->cart)) {
            $this->cart = [
                'prod_1' => ['id' => 'prod_1', 'name' => 'Vino Tinto', 'price' => 12000, 'quantity' => 1, 'seller_id' => 1],
                'prod_2' => ['id' => 'prod_2', 'name' => 'Pisco Artesanal', 'price' => 18000, 'quantity' => 2, 'seller_id' => 2],
            ];
            session()->put('cart', $this->cart);
        }
        */
    }

    public function increase($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated'); // Notifica que el carrito se actualizó
        }
    }

    public function decrease($productId)
    {
        if (isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] > 1) {
            $this->cart[$productId]['quantity']--;
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated'); // Notifica que el carrito se actualizó
        }
    }

    public function remove($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->dispatch('cart-updated'); // Notifica que el carrito se actualizó

            // Si el carrito queda vacío después de eliminar el último producto, cierra el modal
            if (empty($this->cart)) {
                $this->showCheckoutModal = false;
            }
        }
    }

    public function openCheckoutModal()
    {
        if (empty($this->cart)) {
            session()->flash('message', ['type' => 'error', 'text' => 'El carrito está vacío. Agrega productos antes de finalizar la compra.']);
            $this->showCheckoutModal = false; // Asegura que el modal no se muestre
            return;
        }
        $this->showCheckoutModal = true;
    }

    public function closeCheckoutModal()
    {
        $this->showCheckoutModal = false;
    }

    public function checkout()
    {
        // Log para verificar que el método se está ejecutando
        Log::info('CHECKOUT INITIATED', [
            'payment_method' => $this->payment_method,
            'cart_count' => count($this->cart),
            'user_id' => Auth::id()
        ]);

        // 1. Validar que el carrito no esté vacío
        if (empty($this->cart)) {
            session()->flash('error', 'Tu carrito está vacío. Agrega productos antes de confirmar el pedido.');
            $this->closeCheckoutModal();
            return;
        }

        // Validar los datos del formulario del modal
        $this->validate([
            'payment_method' => 'required|string|in:cash,transfer,mercadopago', // ¡Agregamos 'mercadopago'!
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'Debes iniciar sesión para realizar un pedido.');
            return redirect()->route('login'); // Redirige al login si no hay usuario autenticado
        }

        // Calcular el total general del pedido
        $totalOrderAmount = 0;
        foreach ($this->cart as $item) {
            $totalOrderAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        }

        // Generar un ID de referencia para el pedido (útil para Mercado Pago y tus registros)
        $orderReference = 'ORD-' . uniqid();

        // ----------------------------------------------------
        // Lógica para Mercado Pago
        // ----------------------------------------------------
        if ($this->payment_method === 'mercadopago') {
            try {
                // Log del carrito para debug
                Log::info('Cart contents for MercadoPago: ', ['cart' => $this->cart]);
                
                // Prepara los ítems para Mercado Pago (SDK v3 usa arrays)
                $itemsForMercadoPago = [];
                foreach ($this->cart as $item) {
                    $itemsForMercadoPago[] = [
                        'title' => $item['name'] ?? 'Producto',
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'unit_price' => (float) ($item['price'] ?? 0), // Asegúrate de que sea float
                        'description' => $item['name'] ?? 'Producto',
                        'currency_id' => 'CLP', // Moneda para Chile
                    ];
                }

                // Log de los items procesados
                Log::info('MercadoPago items prepared: ', ['items' => $itemsForMercadoPago]);

                if (empty($itemsForMercadoPago)) {
                    session()->flash('error', 'No hay ítems válidos para procesar en Mercado Pago.');
                    $this->closeCheckoutModal();
                    return;
                }

                // Verificar configuración de MercadoPago antes de crear preferencia
                Log::info('MercadoPago config check before creating preference', [
                    'access_token_set' => !empty(env('MERCADOPAGO_ACCESS_TOKEN')),
                    'access_token_prefix' => substr(env('MERCADOPAGO_ACCESS_TOKEN'), 0, 15) . '...'
                ]);

                // Asegurar que MercadoPago esté configurado correctamente antes de crear la preferencia
                MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

                // Crear el cliente de preferencias (SDK v3)
                $client = new PreferenceClient();
                
                // Crear la request de preferencia con URLs absolutas correctas
                $baseUrl = config('app.url', 'http://127.0.0.1:8000');
                
                $preferenceRequest = [
                    "items" => $itemsForMercadoPago,
                    "external_reference" => $orderReference,
                    "back_urls" => [
                        "success" => $baseUrl . '/mercadopago/success?order_ref=' . $orderReference,
                        "pending" => $baseUrl . '/mercadopago/pending?order_ref=' . $orderReference,
                        "failure" => $baseUrl . '/mercadopago/failure?order_ref=' . $orderReference,
                    ]
                    // Removemos notification_url temporalmente para que funcione
                ];

                // Log la request completa antes de enviarla
                Log::info('MercadoPago request to be sent: ', ['request' => $preferenceRequest]);

                $preference = $client->create($preferenceRequest); // Crea la preferencia en Mercado Pago

                // Debug: verificar que se haya creado la preferencia correctamente
                Log::info('MercadoPago Preference created successfully', [
                    'preference_id' => $preference->id ?? 'NO_ID',
                    'sandbox_init_point' => $preference->sandbox_init_point ?? 'NO_SANDBOX_URL',
                    'init_point' => $preference->init_point ?? 'NO_INIT_URL',
                    'external_reference' => $preference->external_reference ?? 'NO_REFERENCE'
                ]);

                // Iniciar transacción de DB para guardar el pedido localmente antes de redirigir
                DB::beginTransaction();
                try {
                    // Crear el PEDIDO principal en la tabla `orders` con estado inicial de Mercado Pago
                    $order = Order::create([
                        'user_id' => $user->id,
                        'status' => 'pending', // Cambiado a 'pending' porque 'pending_mercadopago' no existe en el enum
                        'payment_method' => 'mercadopago',
                        'total' => $totalOrderAmount,
                        'notes' => $this->notes,
                        'reference_id' => $orderReference, // Guarda tu referencia aquí
                        'mp_preference_id' => $preference->id, // Guarda el ID de la preferencia de MP
                    ]);

                    // Recorrer los productos del carrito para crear los ITEMS del PEDIDO
                    foreach ($this->cart as $productId => $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $productId,
                            'seller_id' => $item['seller_id'] ?? null, // Asegúrate de tener seller_id en tu carrito
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 0),
                        ]);
                    }

                    DB::commit(); // Confirma la transacción

                    // Nota: el carrito ahora se vaciará después del pago, no antes de redirigir
                    // session()->forget('cart');
                    // $this->cart = [];
                    // $this->showCheckoutModal = false;

                    // Redirigir al usuario a la URL de pago de Mercado Pago
                    $paymentUrl = env('MERCADOPAGO_ENV') === 'sandbox' ? $preference->sandbox_init_point : $preference->init_point;
                    
                    // Log para debuggear
                    Log::info('MercadoPago payment URL: ' . $paymentUrl);
                    Log::info('Preference object: ', ['preference' => $preference]);
                    
                    // Verificar que la URL no esté vacía
                    if (empty($paymentUrl)) {
                        Log::error('Payment URL is empty. Preference: ', ['preference' => $preference]);
                        session()->flash('error', 'Error: No se pudo generar la URL de pago de MercadoPago.');
                        $this->closeCheckoutModal();
                        return;
                    }
                    
                    // Log para debug de la redirección
                    Log::info('🚀 About to redirect to MercadoPago', [
                        'url' => $paymentUrl, 
                        'preference_id' => $preference->id,
                        'external_reference' => $orderReference
                    ]);
                    
                    // Usar solo JavaScript para la redirección (funciona mejor con Livewire)
                    $this->dispatch('redirect-to-mercadopago', url: $paymentUrl);
                    
                    Log::info('✅ Dispatch sent to frontend', ['event' => 'redirect-to-mercadopago', 'url' => $paymentUrl]);
                    
                    // No retornar redirect() en Livewire, usar solo dispatch

                } catch (\Exception $dbException) {
                    DB::rollBack(); // Si falla el guardado en DB, revierte
                    Log::error('Error al guardar pedido en DB para Mercado Pago: ' . $dbException->getMessage(), ['trace' => $dbException->getTraceAsString()]);
                    session()->flash('error', 'Error interno al procesar el pedido. Por favor, intenta de nuevo.');
                    $this->closeCheckoutModal();
                }

            } catch (MPApiException $mpException) {
                Log::error("Error de API de Mercado Pago: " . $mpException->getMessage(), [
                    'trace' => $mpException->getTraceAsString(),
                    'status_code' => $mpException->getApiResponse()->getStatusCode() ?? 'NO_STATUS',
                    'response_content' => $mpException->getApiResponse()->getContent() ?? 'NO_CONTENT'
                ]);
                session()->flash('error', 'Error al crear preferencia en Mercado Pago: ' . $mpException->getMessage());
                $this->closeCheckoutModal();
            } catch (\Exception $mpException) {
                Log::error("Error general al iniciar pago con Mercado Pago: " . $mpException->getMessage(), [
                    'trace' => $mpException->getTraceAsString(),
                    'file' => $mpException->getFile(),
                    'line' => $mpException->getLine()
                ]);
                session()->flash('error', 'Ocurrió un error al conectar con Mercado Pago. Por favor, intenta de nuevo.');
                $this->closeCheckoutModal();
            }

        }
        // ----------------------------------------------------
        // Lógica para 'cash' o 'transfer' (pagos offline)
        // ----------------------------------------------------
        else {
            DB::beginTransaction(); // Iniciar transacción para pagos offline
            try {
                // Determinar el estado inicial basado en el método de pago
                $initialStatus = ($this->payment_method === 'cash') ? 'pending_cash' : 'pending_transfer';

                // Crear el PEDIDO principal en la tabla `orders`
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => $initialStatus, // Estado inicial del pedido
                    'payment_method' => $this->payment_method,
                    'total' => $totalOrderAmount,
                    'notes' => $this->notes,
                    'reference_id' => $orderReference, // Guarda también la referencia para pedidos offline
                ]);

                // Recorrer los productos del carrito para crear los ITEMS del PEDIDO
                foreach ($this->cart as $productId => $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'seller_id' => $item['seller_id'] ?? null, // Asegúrate de tener seller_id
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 0),
                    ]);
                }

                DB::commit(); // Si todo sale bien, guarda los cambios

                // Limpiar el carrito después de un pedido exitoso
                session()->forget('cart');
                $this->cart = [];
                $this->showCheckoutModal = false;

                // Mensaje de éxito y redirección
                session()->flash('success', '¡Pedido realizado con éxito! Pronto te contactará el vendedor.');
                return redirect()->route('buyer.orders.index'); // Redirige a la página de tus pedidos

            } catch (\Exception $e) {
                DB::rollBack(); // Si algo falla, revierte todos los cambios
                session()->flash('error', 'Ocurrió un error al procesar tu pedido: ' . $e->getMessage());
                Log::error('Error al procesar pedido offline: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                $this->closeCheckoutModal(); // Cierra el modal en caso de error
            }
        }

        if ($this->payment_method === 'mercadopago' && isset($paymentUrl)) {
            session()->forget('cart');
            $this->cart = [];
            $this->showCheckoutModal = false;
        }

        // Verificar callback de éxito de MercadoPago
        if ($this->payment_method === 'mercadopago') {
            $successUrl = $baseUrl . '/mercadopago/success?order_ref=' . $orderReference;
            if (request()->fullUrl() === $successUrl) {
                session()->forget('cart');
                $this->cart = [];
                $this->showCheckoutModal = false;
            }
        }
    }

    // Método para debuggear el cambio de método de pago
    public function updatedPaymentMethod($value)
    {
        Log::info('Payment method changed to: ' . $value);
        // Forzar actualización del componente
        $this->dispatch('payment-method-changed', ['method' => $value]);
    }

    public function render()
    {
        return view('livewire.cart', [
            'cart' => $this->cart
        ]);
    }
}