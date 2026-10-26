<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\Authenticate;

use App\Models\User;
use App\Modules\Auth\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Domain\Exceptions\TooManyLoginAttemptsException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final readonly class UserAuthenticator
{
    /**
     * @return array{token_type: string, access_token: string, user: array{id: int, name: string, email: string}}
     */
    public function __invoke(AuthenticateUserRequest $request): array
    {
        $throttleKey = Str::transliterate(Str::lower($request->email).'|'.$request->clientIp);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw new TooManyLoginAttemptsException($seconds);
        }

        $user = User::query()->where('email', $request->email)->first();

        if ($user === null || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw new InvalidCredentialsException();
        }

        RateLimiter::clear($throttleKey);

        $token = $user->createToken('api_token', ['*'], now()->addDays(7))->plainTextToken;

        return [
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }
}
