<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use stdClass;

class ZohoApi
{
    public $api_domain;
    public $location;
    public $servicename;
    public $scopes = [];
    public $response_type;
    public $redirect_uri;
    public $access_type;
    public $client_id;
    public $client_secret;
    public $authorization_code;
    public $verifySsl;

    public function __construct()
    {
        $user = auth()->user();

        $this->api_domain = 'https://accounts.zoho.eu';
        $this->servicename = 'AaaServer';
        //$this->servicename = 'ZohoCRM';
        $this->scopes = [
            'ZohoCRM.modules.deals.ALL',
            'ZohoCRM.modules.accounts.ALL',
        ];
        $this->response_type = 'code';
        $this->redirect_uri = 'http://romas.website/zoho/get-tokens';
        $this->access_type = 'offline';
        $this->client_id = $user->client_id; //'1000.4TM40PBO11NRN05OMOB84KSQUOOMHN '
        $this->client_secret = $user->client_secret;//'f5e57c883b0c78d1bf9cf3ff80ed10854e1f2474ca
        //$this->client_id = '1000.4TM40PBO11NRN05OMOB84KSQUOOMHN';
        //$this->client_secret = 'f5e57c883b0c78d1bf9cf3ff80ed10854e1f2474ca';
        $this->verifySsl = false;
    }

    public function getLoginUrl()
    {
        $scopes = implode(',', $this->scopes);
        $serviceurl = "{$this->api_domain}/oauth/v2/auth?scope={$scopes}&client_id={$this->client_id}&response_type={$this->response_type}" .
            "&access_type={$this->access_type}&redirect_uri={$this->redirect_uri}";

        return "$this->api_domain/signin?servicename=$this->servicename&serviceurl=" . urlencode($serviceurl);
    }

    public function getTokens(Request $request)
    {
        $user = auth()->user();

        $this->authorization_code = $request->get('code');
        $this->api_domain = $request->get('accounts-server');
        $this->location = $request->get('location');

        $url = "{$this->api_domain}/oauth/v2/token";

        $response = json_decode(Http::asForm()->withOptions(["verify" => $this->verifySsl])->post($url, [
            'grant_type' => 'authorization_code',
            'code' => $this->authorization_code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
        ])->body());

        if (isset($response->error)) {
            var_dump($response->error);
            return $response->error;
        }
        $user->access_token = $response->access_token;
        $user->refresh_token = $response->refresh_token;
        $user->api_domain = $response->api_domain;
        $user->expires_in = $response->expires_in;
        $user->save();
    }

    public function generatetNewAccessToken()
    {
        $user = auth()->user();

        $url = "{$this->api_domain}/oauth/v2/token";

        $response = json_decode(Http::asForm()->withOptions(["verify" => $this->verifySsl])->post($url, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->refresh_token,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ])->body());
        if (isset($response->error)) {
            var_dump($response);
            return $response->error;
        }
        $user->access_token = $response->access_token;
        $user->api_domain = $response->api_domain;
        $user->expires_in = $response->expires_in;

        return $user->save();
    }

    public function findAll($moduleName, $fields)
    {
        //$user = User::find(Auth::id());
        $user = auth()->user();
        if ($user->access_token == null) return 0;
        $recs = json_decode(Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withOptions(["verify" => $this->verifySsl])
            ->get("{$user->api_domain}/crm/v5/{$moduleName}", ['fields' => implode(',', $fields)])->body());

        if (isset($recs->status) && $recs->status == 'error') {
            return $recs;
        }
        if(!isset($recs)) return 0;
        $recs = $recs->data;

        return $recs;
    }

    public function findById($moduleName, $id, $fields)
    {
        //$user = User::find(Auth::id());
        $user = auth()->user();
        if ($user->access_token == null) return 0;
        $recs = json_decode(Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withOptions(["verify" => $this->verifySsl])
            ->get("{$user->api_domain}/crm/v5/{$moduleName}/{$id}", ['fields' => $fields])->body());

        if (isset($recs->status) && $recs->status == 'error') {
            return $recs;
        }
        if(!isset($recs)) return 0;
        $recs = $recs->data[0];

        return $recs;
    }

    public function findBy($moduleName, $attr)
    {
        $user = auth()->user();
        if ($user->access_token == null) return 0;

        $search_val = array_values($attr)[0];
        $search_by = array_key_first($attr);
//var_dump($search_val,$search_by); die;
        $records = json_decode(Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}",
        ])->withOptions(["verify" => $this->verifySsl])
            //->get("{$user->api_domain}/crm/v5/{$moduleName}/search?{$search_by}={$search_val}")->body());
            ->get("{$user->api_domain}/crm/v5/{$moduleName}/search", ['criteria' => "{$search_by}:equals:$search_val"])->body());

        if (isset($records->status) && $records->status == 'error') {
            return $records;
        } else {
            if ($records != null)
                $records = $records->data;
            else return 0;
        }

        return $records;
    }

    public function save($moduleName, $data)
    {
        //$user = User::find(Auth::id());
        $user = auth()->user();

        $dataClass = new StdClass;
        $dataClass->data = [$data];
        $json = json_encode($dataClass);
        $response = json_decode(http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withOptions(["verify" => $this->verifySsl])->withBody($json)
            ->post("{$user->api_domain}/crm/v5/$moduleName")->body());
        return $response->data[0];
    }

    public function delete($moduleName, $id)
    {
        //$user = User::find(Auth::id());
        $user = auth()->user();

        $response = json_decode(http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withOptions(["verify" => $this->verifySsl])
            ->delete("{$user->api_domain}/crm/v5/$moduleName/$id")->body());

        return $response->data[0]->status;


    }
}
