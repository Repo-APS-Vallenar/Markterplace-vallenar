<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class TestMercadoPago extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mercadopago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MercadoPago integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando test de MercadoPago...');
        
        $accessToken = env('MERCADOPAGO_ACCESS_TOKEN');
        
        if (empty($accessToken)) {
            $this->error('❌ MERCADOPAGO_ACCESS_TOKEN no configurado');
            return 1;
        }
        
        $this->info('✅ Access Token: ' . substr($accessToken, 0, 15) . '...');
        
        try {
            // Configurar token
            MercadoPagoConfig::setAccessToken($accessToken);
            $this->info('✅ Token configurado');
            
            // Crear cliente
            $client = new PreferenceClient();
            $this->info('✅ Cliente creado');
            
            // Crear preferencia simplificada
            $request = [
                "items" => [
                    [
                        "title" => "Test Laravel",
                        "quantity" => 1,
                        "unit_price" => 1000.0,
                        "description" => "Test desde Laravel",
                        "currency_id" => "CLP"
                    ]
                ],
                "external_reference" => "LARAVEL-TEST-" . uniqid()
            ];
            
            $this->info('📝 Creando preferencia...');
            $preference = $client->create($request);
            
            $this->info('🎉 ¡Preferencia creada exitosamente!');
            $this->info('ID: ' . $preference->id);
            $this->info('Sandbox URL: ' . $preference->sandbox_init_point);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('❌ Tipo: ' . get_class($e));
            
            // Mostrar detalles del error si es una excepción de MercadoPago
            if (method_exists($e, 'getApiResponse')) {
                $apiResponse = $e->getApiResponse();
                $this->error('📄 Respuesta API completa: ' . print_r($apiResponse, true));
            }
            
            // Mostrar stack trace
            $this->error('🔍 Stack trace: ' . $e->getTraceAsString());
            
            return 1;
        }
    }
}
