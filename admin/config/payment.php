<?php

return [

    /*
    |--------------------------------------------------------------------------
    | payment_details.json path
    |--------------------------------------------------------------------------
    |
    | Absolute path to the JSON file shared with new_front checkout (Paystack
    | keys: paystack_public_key, paystack_secret_key). Leave empty to use the
    | default path next to new_front: ../new_front/admin/public/payment_details.json
    |
    */
    'details_json_path' => env('PAYMENT_DETAILS_JSON_PATH', ''),

];
