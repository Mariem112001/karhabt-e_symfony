<?php
namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use League\OAuth2\Client\Provider\GoogleUser;


class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
private $clientRegistry;
private $entityManager;
private $router;
private $passwordEncoder;

public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, UserPasswordHasherInterface $passwordEncoder)
{
$this->clientRegistry = $clientRegistry;
$this->entityManager = $entityManager;
$this->router = $router;
$this->passwordEncoder = $passwordEncoder;
}

public function supports(Request $request): ?bool
{
    return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
}

public function authenticate(Request $request): Passport
{
    $client = $this->clientRegistry->getClient('google');
    try {
        $accessToken = $this->fetchAccessToken($client);
    } catch (\Exception $e) {
        error_log('Error fetching access token: ' . $e->getMessage());
        throw $e;
    }


return new SelfValidatingPassport(
new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
/** @var GoogleUser $googleUser */
$googleUser = $client->fetchUserFromToken($accessToken);

$email = $googleUser->getEmail();



// 2) do we have a matching user by email?
$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
if(!$user) {
// 3) Maybe you just want to "register" them by creating
// a User object
$user= new User();
$user->setEmail($googleUser->getEmail());
$user->setImageUser($googleUser->getAvatar());
$user->setNom($googleUser->getLastName());
$user->setPrenom($googleUser->getFirstName());
$user->setDateNaissance(new \DateTime('2000-01-01'));
$user->setRole('CLIENT');
$user->setNumTel(11111111);
$encodedPassword = $this->passwordEncoder->hashPassword(
    $user,
    'some_default_password'
);
$user->setPassword($encodedPassword);
$user->setStatus(false);

    
try {
    $this->entityManager->persist($user);
    $this->entityManager->flush();
} catch (\Exception $e) {
    // Log or handle the exception
    error_log($e->getMessage());
}
}
return $user;
})
);
}

public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
// change "app_homepage" to some route in your app
$targetUrl = $this->router->generate('app_user_profile');



return new RedirectResponse($targetUrl);

// or, on success, let the request continue to be handled by the controller
//return null;
}

public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
{
$message = strtr($exception->getMessageKey(), $exception->getMessageData());

return new Response($message, Response::HTTP_FORBIDDEN);
}

/**
* Called when authentication is needed, but it's not sent.
* This redirects to the 'login'.
*/
public function start(Request $request, AuthenticationException $authException = null): Response
{
return new RedirectResponse(
'/login', // might be the site, where users choose their oauth provider
Response::HTTP_TEMPORARY_REDIRECT
);
}
}