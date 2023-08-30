<?php

namespace App\Security;

use App\Security\Exception\InvalidTokenException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AuthorizationTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly ParameterBagInterface $parameters
    ) {}

    /**
     * @inheritDoc
     * @throws InvalidTokenException
     */
    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        if ($accessToken !== $this->parameters->get('authorization_token')) {
            throw new InvalidTokenException();
        }

        return new UserBadge('in_memory_user');
    }
}