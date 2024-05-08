<?php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GoogleConnect extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google", name="connect_google")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to google!
        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                // the scopes you want to access
            ]);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/google/check", name="connect_google_check")
     */
    public function connectCheckAction(Request $request)
{
    if (!$this->getUser()) {
        return new JsonResponse(array('status' => false, 'message' => "User not found!"));
    } else {
        return $this->redirectToRoute('app_home');
    }
}
}