<?php


namespace App\Models;


use App\Services\ZohoApi;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Account
{
    public $accountAttributes = [
        'Account_Name' => '',
        'Website' => '',
        'Phone' => '',
    ];
    public $dealAttributes = [
        'Stage' => '',
        'Deal_Name' => '',
        'Account' => ''
    ];

    public static function getById($id)
    {
        $user = Auth::user();

        $account = json_decode(Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}",
        ])->withOptions(["verify"=>false])
            ->get("{$user->api_domain}/crm/5/Accounts/" . $id))->data;

        if (!empty($account->error)) {

        } else {
            $deal = json_decode(Http::withHeaders([
                'Authorization' => "Zoho-oauthtoken {$user->access_token}",
            ])->withOptions(["verify"=>false])
                ->get("{$user->api_domain}/crm/5/Deals/" . $id . "/Deals?fields=Deal_Name,Stage"))->data;

            return array_replace($account, $deal);
        }
    }

    public static function getAll()
    {
        $user = Auth::user();

        $accounts = json_decode(Http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withOptions(["verify"=>false])
            ->get('https://www.zohoapis.com/crm/5/Accounts'))->data;

        $deals = [];
        foreach ($accounts as $i => $account) {
            $deals[$i] = json_decode(Http::withHeaders([
                'Authorization' => "Zoho-oauthtoken {$user->access_token}",
            ])->withOptions(["verify"=>false])
                ->get("{$user->api_domain}/crm/5/Accounts/" . $account->id . "/Deals?fields=Deal_Name,Stage"))->data;

            $accounts[$i] = array_replace($account, $deals[$i]);
        }

        return $accounts;
    }

    public function save()
    {
        $user = Auth::user();

        $jsonDeal = json_encode(['data' => [$this->dealAttributes]]);
        $responseDeal = json_decode(http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withBody($jsonDeal)
            ->post("{$user->api_domain}/crm/5/Deals"));

        $this->accountAttributes['Deal'] = $responseDeal->data[0]->details->id;
        $jsonAccount = json_encode(['data' => [$this->accountAttributes]]);
        $responseAccount = json_decode(http::withHeaders([
            'Authorization' => "Zoho-oauthtoken {$user->access_token}"
        ])->withBody($jsonAccount)
            ->post("{$user->api_domain}/crm/5/Accounts"));



        return array_replace($responseAccount, $responseDeal);
    }

    public function validate($request)
    {
        $valid=$request->validate([
            'Account_Name'=>"required",
            'Phone'=>"required|phone",
            'Website'=>"required|regex:\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))",
            'Deal_Name'=>"required",
            'Stage'=>"required"
        ]);

        return $valid;
    }
}
