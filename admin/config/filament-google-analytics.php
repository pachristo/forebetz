<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Analytics Property ID
    |--------------------------------------------------------------------------
    | Set ANALYTICS_PROPERTY_ID in .env (e.g. 123456789 for GA4 property).
    */
    'property_id' => env('ANALYTICS_PROPERTY_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Service account credentials
    |--------------------------------------------------------------------------
    | Path to the JSON key file (relative to storage/app or absolute).
    | Create in Google Cloud Console → APIs & Services → Credentials → Service account.
    | Grant the service account "Viewer" on your GA4 property.
    */
    'service_account_credentials' => storage_path('app/analytics/service-account-credentials.json'),
];
