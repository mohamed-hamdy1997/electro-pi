<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    #[OA\Post(
        path: '/api/v1/auth/register',
        summary: 'Register a new user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name',                  type: 'string',  example: 'Mohamed Hamdy'),
                    new OA\Property(property: 'email',                 type: 'string',  format: 'email', example: 'mohamed@example.com'),
                    new OA\Property(property: 'password',              type: 'string',  format: 'password', example: 'password'),
                    new OA\Property(property: 'password_confirmation', type: 'string',  format: 'password', example: 'password'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Registered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Registration successful.'),
                        new OA\Property(property: 'user',    ref: '#/components/schemas/UserResource'),
                        new OA\Property(property: 'token',   type: 'string', example: '1|abc123...'),
                    ],
                ),
            ),
            new OA\Response(response: 422, description: 'Validation error', ref: '#/components/schemas/ValidationError'),
        ],
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => new UserResource($result['user']),
            'token'   => $result['token'],
        ], 201);
    }

    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Login',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email',    type: 'string', format: 'email',    example: 'mohamed@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful.'),
                        new OA\Property(property: 'user',    ref: '#/components/schemas/UserResource'),
                        new OA\Property(property: 'token',   type: 'string', example: '2|xyz789...'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 422, description: 'Validation error', ref: '#/components/schemas/ValidationError'),
        ],
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (! $result) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'user'    => new UserResource($result['user']),
            'token'   => $result['token'],
        ]);
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Logout (revoke current token)',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logged out',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully.'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ],
    )]
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
