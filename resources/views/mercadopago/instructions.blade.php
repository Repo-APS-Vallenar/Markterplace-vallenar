<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrucciones - Marketplace Vallenar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        h1 {
            color: #28a745;
            margin-bottom: 10px;
        }
        .instruction-box {
            background: #f8f9fa;
            border: 2px dashed #6c757d;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .url-box {
            background: #e9ecef;
            border-radius: 5px;
            padding: 10px;
            font-family: monospace;
            font-size: 14px;
            margin: 10px 0;
            word-break: break-all;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="success-icon">✅</div>
        <h1>¡Pago Completado!</h1>
        <p>Tu pago ha sido procesado exitosamente en MercadoPago.</p>
        
        <div class="instruction-box">
            <h3>📋 Para volver a la tienda:</h3>
            <p><strong>Copia y pega esta URL en tu navegador:</strong></p>
            <div class="url-box">
                http://127.0.0.1:8000/mercadopago/success?collection_status=approved&order_ref={{REFERENCE}}
            </div>
            <p><small><em>Reemplaza {{REFERENCE}} con la referencia de tu pedido</em></small></p>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="http://127.0.0.1:8000" class="btn btn-success">🏠 Ir al Marketplace</a>
            <a href="http://127.0.0.1:8000/buyer" class="btn">🛍️ Ver Productos</a>
        </div>
        
        <div style="margin-top: 20px; font-size: 12px; color: #666;">
            <p>💡 <strong>Tip:</strong> En el futuro, esta redirección será automática</p>
        </div>
    </div>
</body>
</html>
