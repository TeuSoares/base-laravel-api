<?php

namespace App\Modules\User\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\User\Actions\UpdatePassword;
use App\Modules\User\Actions\UpdateUser;
use App\Modules\User\Requests\UpdateUserRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function update(
        UpdateUserRequest $request,
        UpdateUser        $updateUser,
        UpdatePassword    $updatePassword,
    ): JsonResponse {
        $data = $request->validated();
        $user = $request->user();

        $user = $updateUser->execute($user, $data);

        if (filled($data['password'] ?? null)) {
            $updatePassword->execute($user, $data['password']);
        }

        return $this->response()
            ->data(data: $user, message: __('user.updated'));
    }
}
