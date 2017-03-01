<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class HrmLoginController extends Controller
{
    public function login(){
      $http = new GuzzleHttp\Client;

      try {
        $response = $http->post('http://hrmoauth.dev/oauth/token', [
          'form_params' => [
              'grant_type' => 'password',
              'client_id' => '1',
              'client_secret' => 'WZhnLA83QQEhcwkBu8LQQJHgTFT5oIBy0j5mz9ZB',
              'username' => 'borer.avis@example.com',
              'password' => 'secret',
              'scope' => '',
          ],
        ]);

        $body = json_decode($response->getBody());
        if($body->access_token){
          session('hrm_auth', true);
        }



      } catch (ClientException $e) {
        $response = $e->getResponse();
        $responseBodyAsString = $response->getBody()->getContents();
        return $responseBodyAsString;
      }

    }
}
