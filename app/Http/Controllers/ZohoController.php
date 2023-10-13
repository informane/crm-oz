<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Services\ZohoApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ZohoController extends Controller
{
    public $zohoApi;

    public function __construct(ZohoApi $zohoApi)
    {
        $this->zohoApi = $zohoApi;
    }

    public function login()
    {
        $user = Auth::User();

        if (!empty($user->access_token)) {
            return redirect('/zoho/list');
        } else {
            //var_dump($this->zohoApi->getLoginUrl()); die;
            return Inertia::location($this->zohoApi->getLoginUrl());
        }
    }

    public function getTokens(Request $request)
    {
        $this->zohoApi->getTokens($request);
    }

    public function list()
    {
        $accounts = Account::getAll();


        return Inertia::render('AccountsDeals', [
            'accounts' => $accounts
        ]);
    }

    public function createAccountDeal()
    {


        return Inertia::render('AccountDealForm', [

        ]);
    }

    public function storeAccountDeal(Request $request)
    {
        $account = new Account();

        if ($account->validate($request)) {
            $account->accountAttributes = $request->all(['Account_Name', 'Phone', 'Website']);
            $account->dealAttributes = $request->all(['Deal_Name', 'Stage']);
            $account->save();

            return redirect('/zoho/list');
        } else {
            back();
        }

    }
}
