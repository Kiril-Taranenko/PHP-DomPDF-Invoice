<?php

require 'vendor/autoload.php';
$client = new \GuzzleHttp\Client();
$url = 'https://api.coingecko.com/api/v3/coins';
$tmp_inv_list = "";
$invoice_list = "";

try {
    $response = $client->request('POST', $url, array(
        'headers' => $headers,
        'body' => $request_body,
    ));
$invoice_data = json_decode($response->getBody()->getContents());
$invoice_id = $invoice_data[0]->id;
}
catch (\GuzzleHttp\Exception\BadResponseException $e) {
print_r($e->getMessage());
}

$datas = $response->json();
        
        // $datas = json_decode($datas);
        // \Log::info($datas);
foreach($datas as $data)
{
    // \Log::info($data->name);
    DB::insert('insert into coin_list (coin_name, coin_id, coin_image) values (?, ?, ?, ?)', [$data->name, $data->id, $data->image[0]]);
}