<?php

namespace App\Http\Controllers;

use App\Services\BitfinexService;
use Illuminate\Http\Request;

class BitfinexController extends Controller
{
    private $bitfinexService = null;

    public function __construct(BitfinexService $bitfinexService)
    {
        $this->bitfinexService = $bitfinexService;
    }

    public function tickers()
    {
        $symbols = request()->get('symbols', 'ALL');

        $ret = $this->bitfinexService->tickers($symbols);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'tickers' => $ret['response']
            ]
        ], 200);
    }

    public function book()
    {
        $symbol = request()->get('symbol');

        $ret = $this->bitfinexService->book($symbol);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'books' => $ret['response']
            ]
        ], 200);
    }

    public function wallets()
    {
        $ret = $this->bitfinexService->wallets();

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        $wallets = [];

        foreach ($ret['response'] as $wallet) {
            if (!isset($wallets[$wallet[0]])) {
                $wallets[$wallet[0]] = [];
            }

            if (!isset($wallets[$wallet[0]][$wallet[1]])) {
                $wallets[$wallet[0]][$wallet[1]] = [
                    'balance' => 0,
                    'available_balance' => 0
                ];
            }

            $wallets[$wallet[0]][$wallet[1]]['balance'] += $wallet[2];
            $wallets[$wallet[0]][$wallet[1]]['available_balance'] += $wallet[4];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'wallets' => $wallets
            ]
        ], 200);
    }

    public function info()
    {
        $ret = $this->bitfinexService->userInfo();

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'info' => $ret['response']
            ]
        ], 200);
    }

    public function fundingLoans()
    {
        $symbols = request()->get('symbols', 'fUST');

        $ret = $this->bitfinexService->fundingLoans($symbols);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'loans' => $ret['response']
            ]
        ], 200);
    }

    public function fundingLoansHistory()
    {
        $symbols = request()->get('symbols', 'fUST');

        $ret = $this->bitfinexService->fundingLoansHistory($symbols);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'loansHistory' => $ret['response']
            ]
        ], 200);
    }

    public function orders_history()
    {
        $symbol = request()->get('symbol', 'tUSTUSD');

        $ret = $this->bitfinexService->ordersHistory($symbol);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        $orders = [];

        foreach ($ret['response'] as $data) {
            $orders[] = [
                'symbol' => $data[3],
                'amount' => $data[6],
                'type' => $data[8],
                'status' => $data[13],
                'price' => $data[16]
            ];
        }

        return response()->json([
            'status' => true,
            'data' => [
                'orders' => $orders
            ]
        ], 200);
    }

    public function orderCreate(Request $request)
    {
        $type = $request->get('type');

        $symbol = $request->get('symbol');

        $price = $request->get('price');

        $amount = $request->get('amount');

        $data = [
            'type' => $type,
            'symbol' => $symbol,
            'price' => $price,
            'amount' => $amount
        ];

        $ret = $this->bitfinexService->orderCreate($data);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'order' => $ret['response']
            ]
        ], 200);
    }

    public function transfer(Request $request)
    {
        $from = $request->get('from');

        $to = $request->get('to');

        $currency = $request->get('currency');

        $amount = $request->get('amount');

        $data = [
            'from' => $from,
            'to' => $to,
            'currency' => $currency,
            'amount' => $amount
        ];

        $ret = $this->bitfinexService->transfer($data);

        if (!$ret['status']) {
            return response()->json([
                'status' => false,
                'msg' => $ret['note']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'order' => $ret['response']
            ]
        ], 200);
    }
}
