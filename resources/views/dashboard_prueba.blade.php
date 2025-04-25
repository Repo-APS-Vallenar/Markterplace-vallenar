@if (Auth::user()->hasRole('admin'))
    <div class="alert alert-success">
        Bienvenido administrador.
    </div>
@elseif (Auth::user()->hasRole('seller'))
    <div class="alert alert-info">
        Bienvenido vendedor.
    </div>
@elseif (Auth::user()->hasRole('buyer'))
    <div class="alert alert-warning">
        Bienvenido comprador.
    </div>
@endif

@if(Auth::user()->hasAnyRole(['admin']))
    <div>
        Tienes acceso como admin.
    </div>
@endif

@if(Auth::user()->hasAnyRole('seller'))
    <div>
        Tienes acceso como seller.
    </div>
@endif

@if(Auth::user()->hasAnyRole('buyer'))
    <div>
        Tienes acceso como buyer.
    </div>
@endif

@can('edit products')
    <div>
        Puedes editar productos.
    </div>
@endcan