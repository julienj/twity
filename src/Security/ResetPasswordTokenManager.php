<?php

namespace App\Security;

class ResetPasswordTokenManager
{
    const TTL = 3600;

    public function generate(): string
    {
        $key = sha1(random_bytes(10));
        $date = new \DateTime();

        return $key.'.'.$date->getTimestamp();
    }

    public function isValid(string $token): bool
    {
        $tokenDate = explode('.', $token);
        $date = new \DateTime();

        $time = $date->getTimestamp() - (int) $tokenDate[1];

        return $time < self::TTL;
    }
}
