<?php 

return [
    'api_url' => env('WATI_API_URL', 'https://api.wati.io/v1'),
    'api_key' => env('WATI_API_KEY', ''),
    'order_success' => env('ORDER_SUCCESS', ''),
    'order_shipped' => env('ORDER_SHIPPED', ''),
    'order_delivered' => env('ORDER_DELIVERED', ''),

];
