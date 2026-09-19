<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(
        LoginRequest $request,
        AccessTokenController $accessTokenController,
        ResponseInterface $psrResponse,
    ): Response {
        $credentials = $request->validated();

        $oauthRequest = SymfonyRequest::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => config('passport.password_client_id'),
            'client_secret' => config('passport.password_client_secret'),
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'scope' => implode(' ', $credentials['scopes'] ?? []),
        ]);

        return $accessTokenController->issueToken(
            (new PsrHttpFactory)->createRequest($oauthRequest),
            $psrResponse,
        );
    }

    public function refreshToken(
        RefreshTokenRequest $request,
        AccessTokenController $accessTokenController,
        ResponseInterface $psrResponse,
    ): Response {
        $credentials = $request->validated();
        $oauthRequest = SymfonyRequest::create('/oauth/token', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $credentials['refresh_token'],
            'client_id' => $credentials['client_id'],
            'client_secret' => $credentials['client_secret'],
            'scope' => $credentials['scope'] ?? '',
        ]);

        return $accessTokenController->issueToken(
            (new PsrHttpFactory)->createRequest($oauthRequest),
            $psrResponse,
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->token()?->revoke();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
