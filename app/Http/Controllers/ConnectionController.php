<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Services\ToolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ConnectionController extends Controller
{
    private $toolService = null;

    public function __construct(ToolService $toolService)
    {
        $this->toolService = $toolService;
    }

    public function show(Connection $connection)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'connection' => [
                    'id' => $connection->id,
                    'api_key' => $this->toolService->substr_cut(Crypt::decrypt($connection->api_key)),
                    'api_secret' => $this->toolService->substr_cut(Crypt::decrypt($connection->api_secret))
                ]
            ]
        ]);
    }

    public function create(Request $request)
    {
        $api_key = Crypt::encrypt($request->get('api_key', ''));

        $api_secret = Crypt::encrypt($request->get('api_secret', ''));

        $user = auth()->user();

        $connection = Connection::find($user->connection_id);

        if (!$connection) {
            $connection = Connection::create([
                'api_key' => $api_key,
                'api_secret' => $api_secret
            ]);
        }

        $connection->api_key = $api_key;
        $connection->api_secret = $api_secret;
        $connection->save();

        $user->connection_id = $connection->id;
        $user->save();

        return response()->json([
            'status' => true,
            'data' => [
                'connection' => $connection
            ]
        ]);
    }
}
