<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\EndSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request, EndSessionService $service): JsonResponse
    {
        if ($request->user() !== null) {
            $service->handle($request);
        }

        return response()->json(status: 204);
    }
}
