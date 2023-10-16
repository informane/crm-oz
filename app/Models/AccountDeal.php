<?php


namespace App\Models;

use App\Services\ZohoApi;
use Illuminate\Support\Facades\Validator;

class AccountDeal
{
    public $accountAttributes = [
        'Account_Name' => '',
        'Website' => '',
        'Phone' => '',
    ];
    public $dealAttributes = [
        'Stage' => '',
        'Deal_Name' => '',
        'AccountDeal' => '',
        'Account_Name' => ''
    ];

    public function validateAndSave($request)
    {

        $validator = Validator::make($request->post(), [
            'Account_Name' => "required|string|max:255",
            'Phone' => ["required", "max:255", "regex:/^\\+?[1-9][0-9]{7,14}$/"],
            'Website' => "required|max:255",
            'Deal_Name' => "required|string|max:255",
            'Stage' => "max:255"
        ]);
        return $validator = $this->saveAccountDeal($validator);

    }

    public function getAccountsDeals()
    {
        $zohoApi = new ZohoApi();

        $records = $zohoApi->findAll('Accounts', ['Account_Name', 'Phone', 'Website']);

        foreach ($records as $i => $record) {

            $related = $zohoApi->findBy('Deals', ['Account_Name' => $record->id]);

            if ($related != null) {
                unset($related[0]->Account_Name);
                $records[$i] = (object)array_replace((array)$record, (array)$related[0]);
            }
        }

        return $records;

    }

    public function saveAccountDeal($validator)
    {
        $zohoApi = new ZohoApi();

        $result = $zohoApi->save('Accounts', $this->accountAttributes);
        if ($result->status == 'error') {
            $validator->errors()->add($result->details->api_name, $result->message);
            return $validator;
        }

        $this->dealAttributes['Account_Name'] = $result->details->id;
        $result = $zohoApi->save('Deals', $this->dealAttributes);
        if ($result->status == 'error') {
            $zohoApi->delete('Accounts', $result->details->id);
            $validator->errors()->add($result->details->api_name, $result->message);
        }

        return $validator;
    }

}
