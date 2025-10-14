<?php

namespace App\Mercure;

use Lcobucci\JWT\Configuration;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MercureTokenProvider implements TokenProviderInterface
{
    private $tokenStorage;
    private $jwtConfig;

    public function __construct(TokenStorageInterface $tokenStorage, string $secret)
    {
        $this->tokenStorage = $tokenStorage;
        $this->jwtConfig = Configuration::forSymmetricSigner(
            new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText($secret)
            );
    }

    public function getJwt(): string
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        $topics = [];
        if ($user) {
            foreach ($user->getTeachingCourses() as $course) {
                $topics[] = "http://localhost/course/{$course->getId()}/chat";
            }
            foreach ($user->getStudentCourses() as $course) {
                $topics[] = "http://localhost/course/{$course->getId()}/chat";
            }
        }

        $now   = new \DateTimeImmutable();
        $token = $this->jwtConfig->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+5 minute'))
            ->withClaim('mercure', [
                'subscribe' => $topics,
            ])
            ->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey());

        return $token->toString();
    }
}
