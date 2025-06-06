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
    <livewire:cart />
</div>

<div id="cart-loader" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:1000;justify-content:center;align-items:center;background:rgba(255,255,255,0.5)">
    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
</div>

<script>
(function() {
    if (window.__cartListenerAdded) return;
    window.__cartListenerAdded = true;

    function showCartMessage(msg, type = 'success') {
        const el = document.getElementById('cart-messages');
        el.innerHTML = `<div class='mb-4 p-4 rounded border ${type==='success' ? 'border-green-400 bg-green-100 text-green-800' : 'border-red-400 bg-red-100 text-red-800'} flex justify-between items-center'>${msg}<button onclick='this.parentElement.remove()' class='ml-4'>&times;</button></div>`;
        setTimeout(() => { el.innerHTML = ''; }, 3000);
    }

    function setCartLoading(loading) {
        document.getElementById('cart-loader').style.display = loading ? 'flex' : 'none';
        document.querySelectorAll('.cart-btn').forEach(btn => btn.disabled = loading);
        const cartDiv = document.getElementById('cart-content');
        if (cartDiv) {
            if (loading) cartDiv.classList.add('loading');
            else cartDiv.classList.remove('loading');
        }
    }

    function updateDOMQuantity(productId, delta) {
        const input = document.querySelector(`.cart-qty-input[data-id='${productId}']`);
        if (!input) return;
        let oldValue = parseInt(input.value);
        let newValue = oldValue + delta;
        if (newValue < 1) newValue = 1;
        input.value = newValue;
        let totalItems = 0;
        let totalPrice = 0;
        document.querySelectorAll('.cart-qty-input').forEach(inp => {
            const qty = parseInt(inp.value);
            const price = parseInt(inp.closest('tr').querySelector('td:nth-child(3)').textContent.replace(/[^\d]/g, ''));
            totalItems += qty;
            totalPrice += qty * price;
        });
        document.getElementById('cart-total-items').textContent = totalItems;
        document.getElementById('cart-total-price').textContent = '$' + totalPrice.toLocaleString('es-CL');
    }

    async function updateCart(productId, action) {
        updateDOMQuantity(productId, action === 'increase' ? 1 : -1);
        setCartLoading(true);
        const res = await fetch(`/cart/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: new URLSearchParams({
                _method: 'PUT',
                action: action
            })
        });
        setCartLoading(false);
        if (!res.ok) {
            location.reload();
            showCartMessage('Error al actualizar el carrito', 'error');
        } else {
            showCartMessage('Carrito actualizado');
        }
    }

    async function removeFromCart(productId) {
        const row = document.querySelector(`.cart-btn-del[data-id='${productId}']`)?.closest('tr');
        if (row) {
            row.remove();
            let totalItems = 0;
            let totalPrice = 0;
            document.querySelectorAll('.cart-qty-input').forEach(inp => {
                const qty = parseInt(inp.value);
                const price = parseInt(inp.closest('tr').querySelector('td:nth-child(3)').textContent.replace(/[^\d]/g, ''));
                totalItems += qty;
                totalPrice += qty * price;
            });
            document.getElementById('cart-total-items').textContent = totalItems;
            document.getElementById('cart-total-price').textContent = '$' + totalPrice.toLocaleString('es-CL');
        }
        setCartLoading(true);
        const res = await fetch(`/cart/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: new URLSearchParams({
                _method: 'DELETE'
            })
        });
        setCartLoading(false);
        if (!res.ok) {
            location.reload();
            showCartMessage('Error al eliminar', 'error');
        } else {
            showCartMessage('Producto eliminado');
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('cart-btn-inc')) {
            e.preventDefault();
            updateCart(e.target.dataset.id, 'increase');
        } else if (e.target.classList.contains('cart-btn-dec')) {
            e.preventDefault();
            updateCart(e.target.dataset.id, 'decrease');
        } else if (e.target.classList.contains('cart-btn-del')) {
            e.preventDefault();
            removeFromCart(e.target.dataset.id);
        }
    });
})();
</script>
@endsection 