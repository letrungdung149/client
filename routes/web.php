<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('prepare-to-login', function () {
    $query = http_build_query([
        'client_id' => '97a1a16b-4e47-4dc2-a4be-235afaf2f4c4',
        'redirect_url' => 'http://127.0.0.1:8081/callback',
        'response_type' => 'code',
        'scope' => '',
    ]);
    return redirect(to: 'http://127.0.0.1:8000/' . 'oauth/authorize?' . $query);
})->name('prepare-login');

Route::get('callback', function (Request $request) {
    $response = Http::post('http://127.0.0.1:8000/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => '97a1a16b-4e47-4dc2-a4be-235afaf2f4c4',
            'client_secret' => 'VJGcZbuabExdHGEAKsLKo7SQU3i3noEuivoJqvW0',
            'redirect_uri' => 'http://127.0.0.1:8081/callback',
            'code' => $request->code
        ],
    ]);
    $body = json_decode((string)$response->getBody(), true);

    $response = Http::get('http://127.0.0.1:8000/api/todo-list', [
        'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $body['access_token'],
        ],
    ]);
    return json_decode((string) $response->getBody(), true);
});
