<?php

namespace App\Services;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

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
        $url = $this->baseUrl . "/sendTemplateMessage?WhatsAppNumber=$phoneNumber";
        log::info($url);
        log::info($params);

        try {
            // $response = Http::withToken($this->token)
            //     ->withHeaders([
            //         'Content-Type' => 'application/json',
            //     ])
            //     ->post($url, [
            //         'template_name' => $template_name,
            //         'broadcast_name' => $broadcast_name,
            //         'parameters' => $params
            //     ]);

            //     $response = Http::withHeaders([
            //         'Authorization' => 'Bearer ' . $this->token,
            //         'Content-Type' => 'application/json'
            //     ])->post($url, [
            //         'template_name' => $template_name,
            //         'broadcast_name' => $broadcast_name,
            //         'parameters' => $params
            //     ]);
            $client = new Client();

$response = $client->request('POST', $url,  [
                    'template_name' => $template_name,
                    'broadcast_name' => $broadcast_name,
                    'parameters' => $params
                ]);
log::info([
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
