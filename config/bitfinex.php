<?php

return [

    'api' => [
        'key' => env('BITFINEX_API_KEY'),
        'secret' => env('BITFINEX_API_SECRET')
    ],
    'public' => [
        'tickers' => 'https://api-pub.bitfinex.com/v2/tickers',
        'book' => 'https://api-pub.bitfinex.com/v2/book/%SYMBOL%/P0'
    ],
    'auth' => [
        'url' => 'https://api.bitfinex.com/',
        'wallets' => 'https://api.bitfinex.com/v2/auth/r/wallets',
        'user_info' => 'https://api.bitfinex.com/v2/auth/r/info/user',
        'orders_history' => 'https://api.bitfinex.com/v2/auth/r/orders/hist',
        'order_submit' => 'https://api.bitfinex.com/v2/auth/w/order/submit',
        'funding_loans' => 'https://api.bitfinex.com/v2/auth/r/funding/loans/%SYMBOL%',
        'funding_loans_history' => 'https://api.bitfinex.com/v2/auth/r/funding/loans/%SYMBOL%/hist',
        'transfer' => 'https://api.bitfinex.com/v2/auth/w/transfer'
    ]
];
