@extends('layouts.app')

@section('content')
<style>
    .cart-btn {
        border-radius: 9999px;
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        transition: background 0.2s, color 0.2s, transform 0.1s;
        box-shadow: 0 2px 8px #0001;
        border: none;
        outline: none;
        cursor: pointer;
    }
    .cart-btn-inc { background: #d1fae5; color: #059669; }
    .cart-btn-inc:hover { background: #6ee7b7; color: #065f46; transform: scale(1.1); }
    .cart-btn-dec { background: #fef9c3; color: #b45309; }
    .cart-btn-dec:hover { background: #fde68a; color: #92400e; transform: scale(1.1); }
    .cart-btn-del { background: #fee2e2; color: #dc2626; }
    .cart-btn-del:hover { background: #fca5a5; color: #991b1b; transform: scale(1.1) rotate(-10deg); }
    .cart-btn:active { transform: scale(0.95); }
    .cart-sticky {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 10;
        box-shadow: 0 -2px 8px #0001;
        padding: 1rem 0;
    }
    .cart-anim {
        animation: cartFadeIn 0.4s;
    }
    @keyframes cartFadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: none; }
    }
</style>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center gap-2 mb-2">
        <span class="text-3xl text-blue-500 animate-bounce"><i class="fas fa-shopping-cart"></i></span>
        <h1 class="text-2xl font-bold">Mi carrito</h1>
    </div>
    <div id="cart-messages"></div>
    @if(empty($cart) || count($cart) === 0)
        <div class="flex flex-col items-center justify-center py-12">
            <span class="text-6xl mb-4">🛒</span>
            <p class="text-lg text-gray-600 mb-2">Tu carrito está vacío.</p>
            <a href="{{ route('buyer.products.index') }}" class="mt-2 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Seguir comprando</a>
        </div>
    @else
        <div id="cart-content" class="cart-anim">
            @include('buyer.cart.partials.table', ['cart' => $cart])
        </div>
    @endif
</div>

<script>
function showCartMessage(msg, type = 'success') {
    const el = document.getElementById('cart-messages');
    el.innerHTML = `<div class='mb-4 p-4 rounded border ${type==='success' ? 'border-green-400 bg-green-100 text-green-800' : 'border-red-400 bg-red-100 text-red-800'} flex justify-between items-center'>${msg}<button onclick='this.parentElement.remove()' class='ml-4'>&times;</button></div>`;
    setTimeout(() => { el.innerHTML = ''; }, 3000);
}

async function updateCart(productId, action) {
    console.log('updateCart', productId, action);
    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    formData.append('action', action);
    const res = await fetch(`/cart/${productId}`, { method: 'POST', body: formData });
    if (res.ok) {
        const html = await res.text();
        console.log('HTML recibido:', html);
        const cartDiv = document.getElementById('cart-content');
        cartDiv.innerHTML = html;
        showCartMessage('Carrito actualizado');
    } else {
        showCartMessage('Error al actualizar el carrito', 'error');
    }
}

async function removeFromCart(productId) {
    if (!confirm('¿Eliminar este producto del carrito?')) return;
    console.log('removeFromCart', productId);
    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'DELETE');
    const res = await fetch(`/cart/${productId}`, { method: 'POST', body: formData });
    if (res.ok) {
        const html = await res.text();
        console.log('HTML recibido:', html);
        const cartDiv = document.getElementById('cart-content');
        cartDiv.innerHTML = html;
        showCartMessage('Producto eliminado');
    } else {
        showCartMessage('Error al eliminar', 'error');
    }
}

(function() {
    if (window.__cartListenerAdded) return;
    window.__cartListenerAdded = true;
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('cart-btn-inc')) {
            e.preventDefault();
            e.stopPropagation();
            const id = e.target.dataset.id;
            console.log('click +', id);
            updateCart(id, 'increase');
        }
        if (e.target.classList.contains('cart-btn-dec')) {
            e.preventDefault();
            e.stopPropagation();
            const id = e.target.dataset.id;
            console.log('click -', id);
            updateCart(id, 'decrease');
        }
        if (e.target.classList.contains('cart-btn-del')) {
            e.preventDefault();
            e.stopPropagation();
            const id = e.target.dataset.id;
            console.log('click eliminar', id);
            removeFromCart(id);
        }
    }, false);
})();
</script>
@endsection 