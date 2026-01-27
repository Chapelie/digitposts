<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class CustomUserProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        $user = User::find($identifier);
        if (!$user) {
            return null;
        }

        $rememberToken = $user->getRememberToken();
        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        // Si on a 'email' dans les credentials, chercher par email
        if (isset($credentials['email'])) {
            return User::where('email', $credentials['email'])->first();
        }

        // Si on a 'phone' dans les credentials, chercher par téléphone
        if (isset($credentials['phone'])) {
            return User::where('phone', $credentials['phone'])->first();
        }

        // Si on a 'identifier', déterminer si c'est email ou téléphone
        if (isset($credentials['identifier'])) {
            $identifier = $credentials['identifier'];
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                return User::where('email', $identifier)->first();
            } else {
                return User::where('phone', $identifier)->first();
            }
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        return \Hash::check($plain, $user->getAuthPassword());
    }
}
