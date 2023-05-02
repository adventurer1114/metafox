<?php

namespace MetaFox\Mfa\Listeners;

use MetaFox\Mfa\Support\Facades\Mfa;

class MfaListener
{
    public function requestMfaToken($user)
    {
        if ($user && Mfa::hasMfaEnabled($user)) {
            return [
                'mfa_token' => Mfa::requestMfaToken($user),
            ];
        }
    }

    public function validateForPassportPasswordGrant($user, $input)
    {
        if ($user && Mfa::hasMfaEnabled($user)) {
            return Mfa::isAuthenticated($user, $input);
        }
    }

    public function hasMfaEnabled($user)
    {
        return !!Mfa::hasMfaEnabled($user);
    }
}