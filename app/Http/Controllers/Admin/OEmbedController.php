<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RicardoFiorani\OEmbed\OEmbed;

class OEmbedController extends Controller
{
    public function getOEmbed(Request $request): JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(null);
        }

        $service = new OEmbed(null, null, null, false);

        $uri = new Uri($request->url);

        try {
            $result = $service->get(
                $uri,
                480,
                300,
                ['omitscript' => true]
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'iframe' => (string) $result,
        ]);
    }
}
