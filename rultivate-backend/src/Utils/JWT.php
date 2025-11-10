<?php
namespace Rultivate\Utils;

use Firebase\JWT\JWT as FirebaseJwt;
use Firebase\JWT\Key;
use DateTimeImmutable;

class JWT
{
    private string $secret;
    private string $issuer;
    private string $audience;
    private int $expiry;

    public function __construct(array $config)
    {
        $this->secret = $config['secret'];
        $this->issuer = $config['issuer'];
        $this->audience = $config['audience'];
        $this->expiry = $config['expiry'];
    }

    public function create(array $payload): string
    {
        $now = new DateTimeImmutable();
        $token = array_merge($payload, [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $now->getTimestamp(),
            'nbf' => $now->getTimestamp(),
            'exp' => $now->getTimestamp() + $this->expiry
        ]);
        return FirebaseJwt::encode($token, $this->secret, 'HS256');
    }

    public function verify(string $jwt): array
    {
        return (array) FirebaseJwt::decode($jwt, new Key($this->secret, 'HS256'));
    }
}
