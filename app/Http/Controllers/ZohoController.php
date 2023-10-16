<?php

namespace App\Http\Controllers;

use App\Models\AccountDeal;
use App\Services\ZohoApi;
use Illuminate\Http\Request;

use Inertia\Inertia;

class ZohoController extends Controller
{
    public $zohoApi;

    public function __construct()
    {

    }

    public function login()
    {
        $user = auth()->user();

        if ($user->access_token != null) {
            return Inertia::location('/zoho/list');
        } else {

            return Inertia::location($this->zohoApi->getLoginUrl());
        }
    }

    public function getTokens(Request $request)
    {
        $this->zohoApi = new ZohoApi();
        $this->zohoApi->getTokens($request);

        return Inertia::location('/zoho/list');
    }

    public function list(Request $request)
    {
        $this->zohoApi = new ZohoApi();
        $account = new AccountDeal();
        $accounts = $account->getAccountsDeals();
        //$user = auth()->user();

        return Inertia::render('AccountDeals', [
            'accounts' => $accounts,
            'user' => \Illuminate\Support\Facades\Auth::user(),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function createAccountDeal(Request $request)
    {
        $this->zohoApi = new ZohoApi();
        return Inertia::render('AccountDealForm', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function saveAccountDeal(Request $request)
    {
        $this->zohoApi = new ZohoApi();
        $account = new AccountDeal();

        $account->accountAttributes = $request->all(['Account_Name', 'Phone', 'Website']);
        $account->dealAttributes = $request->all(['Deal_Name', 'Stage']);
        $validator = $account->validateAndSave($request);

        if (count($validator->errors())) {
            return back()->withErrors($validator)->withInput();

        } else {
            return Inertia::location('/zoho/list');
        }
    }
}
