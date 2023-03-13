<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BitfinexService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private $bitfinexService = null;

    public function __construct(BitfinexService $bitfinexService)
    {
        $this->bitfinexService = $bitfinexService;
    }

    public function register(Request $request)
    {
        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ];

        $user = User::create($data);

        return response()->json([
            'status' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function login(Request $request)
    {
        $data = [
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ];

        if (!$jwt_token = auth()->attempt($data)) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'login' => [
                        '無效的 Email 或 密碼'
                    ]
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'status' => true,
            'token' => "Bearer {$jwt_token}",
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'msg' => 'OK'
        ]);
    }

    public function userInfo()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'user' => auth()->user()
            ]
        ]);
    }
}
