<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use GuzzleHttp\Client;

class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSms($text){

        $client = new Client();

        $response = $client->get('http://api.itelservices.com/send.php', [
            'query' => [
                'user' => 'asaanghar',
                'pass' => 'n9mOT',
//                'number' => preg_replace('/^0/', '92', $user->mobile_no),
                'number' => 923216528522,
                'text' => $text,
                'from' => 44731,
                'type' => 'sms',
                'saccount_id' => 1531614412,
                'transaction_id' => uniqid()
            ]
        ]);

        // Check if request was successful
        if ($response->getStatusCode() == 200) {
            // Success
            $responseData = $response->getBody()->getContents();
            // Handle response data
        } else {
            // Request failed
            $error = $response->getBody()->getContents();
            // Handle error
        }

    }

}
