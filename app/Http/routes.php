<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('home', 'PortalController@redirect');
Route::get('callback', 'PortalController@callback');

// Route::get('/home', function(){
//
//
//     $root = 'https://api.cc.ncu.edu.tw/oauth';
//     $client = 'NjAwZjVlYWUtZmYxYi00ZDJjLTgwM2QtMTc3MTM5YWZmNzA0';
//
//     $scope = 'user.info.basic.read';
//     $url = $root . '/oauth/authorize?response_type=code&scope=' . $scope . '&client_id=' . $client;
//
//     return redirect($url);
// });

// Route::get('/callback', function()
// {
//   $root = 'https://api.cc.ncu.edu.tw/oauth';
//   $client_id = 'NjAwZjVlYWUtZmYxYi00ZDJjLTgwM2QtMTc3MTM5YWZmNzA0';
//   $secret = 'b7c564bd9ac24ba229770f9584835a41af63438149f36cdc48f3a8ac1a027291047fdf18eba892bd86515b64a56c7ee2e1456e818f09dabacdd524f14a5ec1c1';
//
//     $url = $root . '/oauth/token';
//
//     $response = Guzzle::post(
//         $url,
//         [
//             'form_params' => [
//               'grant_type' => 'authorization_code',
//               'code' => $_GET['code'],
//               'client_id' => $client_id,
//               'client_secret' => $secret,
//             ]
//         ]
//     );
//     //echo $response->getBody();
//     // echo $response->getStatusCode(); // 200
//     // echo $response->getReasonPhrase(); // OK
//     // echo $response->getProtocolVersion(); // 1.1
//
//     $data = json_decode($response->getBody());
//     echo $data->{'access_token'};
//     session_start();
//     $_SESSION['a'] = $data->{'access_token'};
//     return redirect('/');
// });

Route::get('/info', function(){
  $root = 'https://api.cc.ncu.edu.tw';
  $url = $root . '/personnel/v1/info';
  session_start();
  $response = Guzzle::get(
      $url,
      [
            'headers'  => [
                'Authorization' => 'Bearer ' . session('access_token')
            ]

      ]
  );
  $data = json_decode($response->getBody());

  echo $data->{'id'};
  echo $data->{'name'};
  echo $data->{'type'};
  echo $data->{'unit'};


  //$config = $this->app['config']['portal'];
  //echo $config['client_id'] . ',' . $config['client_secret'];
});
