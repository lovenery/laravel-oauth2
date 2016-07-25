<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Guzzle;
use App\User;
use App\SocialAccount;

class PortalController extends Controller
{
    public function redirect()
    {
        $root = 'https://api.cc.ncu.edu.tw/oauth';
        $client_id = config('portal.client_id');
        $scope = 'user.info.basic.read';
        $url = $root . '/oauth/authorize?response_type=code&scope=' . $scope . '&client_id=' . $client_id;

        return redirect($url);
    }

    public function callback(Request $request)
    {
        if(!isset($_GET['code'])){
            return redirect('/');
        }
        $root = 'https://api.cc.ncu.edu.tw/oauth';
        $client_id = config('portal.client_id');
        $client_secret = config('portal.client_secret');
        $url = $root . '/oauth/token';

        $response = Guzzle::post(
            $url,
            [
                'form_params' => [
                  'grant_type' => 'authorization_code',
                  'code' => $_GET['code'],
                  'client_id' => $client_id,
                  'client_secret' => $client_secret,
                ]
            ]
        );

        $data = json_decode($response->getBody());
        //echo $data->{'access_token'};
        $request->session()->put('access_token', $data->{'access_token'});
        $value = $request->session()->get('access_token');
        echo $value;
        //session_start();
        //$_SESSION['a'] = $data->{'access_token'};
        $user = $this->createOrGetUser($request);
        auth()->login($user);
        return redirect('/');
    }
    public function createOrGetUser(Request $request)
    {
        $portal = $this->getUserInfo($request);
        $account = SocialAccount::whereProvider('portal')
            ->whereProviderUserId($portal->{'id'})
            ->first();

        if ($account) {
            return $account->user; // 有帳號會出現在這
        } else {

            $account = new SocialAccount([
                'provider_user_id' => $portal->{'id'},
                'provider' => 'portal'
            ]);

            $user = User::where('name','=',$portal->{'id'})->first();

            if (!$user) {

                $user = User::create([
                    'email' => time(),
                    'name' => $portal->{'id'},
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            return $user;

        }
    }
    public function getUserInfo(Request $request)
    {
        $root = 'https://api.cc.ncu.edu.tw';
        $url = $root . '/personnel/v1/info';
        $access_token = $request->session()->get('access_token');
        $response = Guzzle::get(
            $url,
            [
                'headers'  => [ 'Authorization' => 'Bearer ' . $access_token ]
            ]
        );
        $data = json_decode($response->getBody());
        return $data;
        // echo $data->{'id'};
        // echo $data->{'name'};
        // echo $data->{'type'};
        // echo $data->{'unit'};
    }
}
