<?php


namespace PixiiBomb\Api\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PixiiBomb\Api\Enums\Action;
use PixiiBomb\Api\Validation\UserValidation;

class UserController extends ApiController
{
    protected function model(): string
    {
        return User::class;
    }

    protected function validator(): string
    {
        return UserValidation::class;
    }

    public function create(Request $request): JsonResponse
    {
        $data = $this->validate($request, Action::CREATE);

        if (User::query()->where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['The email has already been taken.'],
            ]);
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::query()->create($data);
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'ok' => true,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $currentAccessToken = auth()->user()->currentAccessToken();
        $bearerToken = $request->bearerToken();

        return response()->json([
            'user' => $request->user(),
            'token' => $bearerToken,
            'token_type' => 'Bearer',
            'current_access_token' => $currentAccessToken,
        ]);
    }

    public function patchAvatar(Request $request, int|string $id): JsonResponse
    {
        $user = $this->findRecordOrFail($this->model(), $id);

        Gate::authorize('update', $user);

        $this->validate($request, Action::PATCH, ['avatar']);

        if (!$request->hasFile('avatar')) {
            abort(response()->json([
                'error' => 'No avatar file was uploaded.',
            ], 422));
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update([
            'avatar' => $path,
        ]);

        return response()->json([
            'data' => $user->fresh(),
        ], 200);
    }
}
