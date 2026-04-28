<?php

namespace App\Modules\Auth\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Auth\Actions\ForgotPasswordAction;
use App\Modules\Auth\Actions\LoginAction;
use App\Modules\Auth\Actions\LogoutAction;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\Actions\ResetPasswordAction;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request, LoginAction $loginAction): JsonResponse
    {
        $user = $loginAction->execute($request->validated());

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->response()->data(data: $user, message: __('auth.login_success'));
    }

    public function logout(Request $request, LogoutAction $logoutAction): JsonResponse
    {
        $logoutAction->execute();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->response()->message(__('auth.logout_success'));
    }

    public function register(RegisterRequest $request, RegisterAction $registerAction): JsonResponse
    {
        $user = $registerAction->execute($request->validated());

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->response()->data(data: $user, message: __('auth.register_success'), status: 201);
    }

    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $forgotPasswordAction): JsonResponse
    {
        $data = $request->validated();

        $forgotPasswordAction->execute($data);

        $email = $data['email'];

        return $this->response()->message(
            __('auth.forgot_password_info', ['email' => $email])
        );
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $resetPasswordAction): JsonResponse
    {
        $resetPasswordAction->execute($request->validated());
        return $this->response()->message(__('auth.password_reset_success'));
    }

    public function me(Request $request): JsonResponse
    {
        return $this->response()->data($request->user());
    }
}
