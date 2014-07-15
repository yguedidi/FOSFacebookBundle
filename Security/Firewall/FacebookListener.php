<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Security\Firewall;

use YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

/**
 * Facebook authentication listener.
 */
class FacebookListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        $accessToken = $request->get('access_token');

        return $this->authenticationManager->authenticate(new FacebookToken($this->providerKey, '', array(), $accessToken));
    }
}
