<?php

namespace App\Mercure;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JwtProvider
{

    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __invoke() :string
    {
        return (new Builder())
            ->set('mercure', ['publish' => ['*']])
            ->sign(new Sha256(), $this->key)
            ->getToken();
    }
}
