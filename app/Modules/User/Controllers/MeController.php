<?php

namespace App\Modules\User\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->response()->data(new UserResource($request->user()));
    }
}
