<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class StatusCheckUserProvider extends EloquentUserProvider
{
    public function validateCredentials(UserContract $user, array $credentials)
    {
        if ($user->status === 'inactive') {
            throw new \Exception('Your account has been deactivated. Please contact the administrator.');
        }

        return parent::validateCredentials($user, $credentials);
    }
}
