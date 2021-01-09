<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{

    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->encoder = $userPasswordEncoderInterface;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === "security_login" && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return $request->request->get('login');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try 
        {
            return $userProvider->loadUserByUsername($credentials['email']);
        }
        catch(UsernameNotFoundException $exception)
        {
            throw new AuthenticationException("Cette adresse email n'est pas connue");
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);

        if(!$isValid) 
        {
            throw new AuthenticationException("Mot de passe incorrect");
        }
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $login = $request->request->get('login');
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception, $login);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse('/');
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
