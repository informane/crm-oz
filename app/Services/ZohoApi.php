<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ZohoApi
{
    public $api_domain;
    public $accounts_domain;
    public $location;
    public $servicename;
    public $scopes = [];
    public $response_type;
    public $redirect_uri;
    public $access_type;
    public $client_id;
    public $client_secret;
    public $authorization_code;

    public function __construct()
    {
        $this->api_domain = 'https://www.zohoapis.eu';
        $this->accounts_domain = 'https://accounts.zoho.com';
        $this->servicename = 'ZohoCRM';
        $this->scopes = [
            'ZohoCRM.settings.modules.ALL',
            //'ZohoCRM.modules.deals.ALL',
        ];
        $this->response_type = 'code';
        $this->redirect_uri = 'http://crmoz/zoho/get-tokens';
        $this->access_type = 'offline';
        $this->client_id = '1000.4TM40PBO11NRN05OMOB84KSQUOOMHN';
        $this->client_secret = 'f5e57c883b0c78d1bf9cf3ff80ed10854e1f2474ca';

       ;
    }

    public function getLoginUrl()
    {
        $scopes = implode(',', $this->scopes);
        $serviceurl = "{$this->api_domain}/oauth/v2/auth?scope={$scopes}&client_id={$this->client_id}&response_type={$this->response_type}".
        "&access_type={$this->access_type}&redirect_uri={$this->redirect_uri}";

        return "$this->accounts_domain/signin?servicename=$this->servicename&serviceurl=".urlencode($serviceurl);
    }

    public function getTokens(Request $request)
    {
        $user_id = Auth::id();
        $user = User::where(['id' => $user_id])->first();

        $this->authorization_code = $request->get('code');
        $this->accounts_domain = $request->get('accounts_server');
        $this->location = $request->get('domain');

        $url = "{$this->accounts_domain}/oauth/v2/token";

        $response = json_decode(Http::post($url, [
            'grant_type' => 'authorization_code',
            'code' =>  $this->authorization_code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
        ]));


        $user->access_token = $response->access_token;
        $user->refresh_token = $response->refresh_token;
        $user->api_domain = $response->api_domain;
        $user->expires_in = $response->expires_in;
        $user->save();
    }

    public function getNewAccessToken()
    {
        $user_id = Auth::id();
        $user = User::where(['id' => $user_id])->first();

        $url = "{$this->accounts_domain}/oauth/v2/token";

        $response = json_decode(Http::post($url, [
            'grant_type' => 'refresh_token',
            'refresh_token' =>  $user->refresh_token,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]));

        $user->access_token = $response->access_token;
        $user->api_domain = $response->api_domain;
        $user->expires_in = $response->expires_in;
        $user->save();
    }
}
