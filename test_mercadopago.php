<?php

require_once __DIR__ . '/vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Testing MercadoPago credentials...\n";
echo "Access Token: " . substr($_ENV['MERCADOPAGO_ACCESS_TOKEN'], 0, 20) . "...\n";

MercadoPagoConfig::setAccessToken($_ENV['MERCADOPAGO_ACCESS_TOKEN']);

$client = new PreferenceClient();

try {
    $testRequest = [
        'external_reference' => 'test-' . time(),
        'items' => [
            [
                'title' => 'Test Product',
                'quantity' => 1,
                'unit_price' => 1000,
                'currency_id' => 'CLP'
            ]
        ],
        'back_urls' => [
            'success' => 'http://localhost:8000/test/success',
            'pending' => 'http://localhost:8000/test/pending',
            'failure' => 'http://localhost:8000/test/failure'
        ]
    ];
    
    echo "Creating preference...\n";
    $preference = $client->create($testRequest);
    echo "SUCCESS! Preference created with ID: " . $preference->id . "\n";
    echo "Sandbox URL: " . $preference->sandbox_init_point . "\n";
    
} catch (MPApiException $e) {
    echo "API ERROR: " . $e->getMessage() . "\n";
    echo "Status Code: " . $e->getApiResponse()->getStatusCode() . "\n";
    echo "Response Content: " . json_encode($e->getApiResponse()->getContent(), JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage() . "\n";
}
