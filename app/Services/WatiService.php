<?php

namespace App\Services;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class WatiService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = env('WATI_API_URL');
        $this->token = env('WATI_API_KEY');
    }

    public function sendMessage($phoneNumber, $template_name, $broadcast_name, $params = [])
    {

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . "/sendTemplateMessage/$phoneNumber", [
                'template_name' => $template_name,
                'broadcast_name' => $broadcast_name,
                'parameters' => $params
            ]);
            log::info($response);
            return true;
        } catch (\Exception $e) {
            log::info($e);
            return false;
        }
    }
}
