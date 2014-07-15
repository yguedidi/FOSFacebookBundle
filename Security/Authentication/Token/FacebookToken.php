<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class FacebookToken extends AbstractToken
{
    private $providerKey;

    protected $accessToken;

    public function __construct($providerKey, $uid = '', array $roles = array(), $accessToken = null)
    {
        parent::__construct($roles);

        $this->setUser($uid);

        if (!empty($uid)) {
            $this->setAuthenticated(true);
        }

        $this->providerKey = $providerKey;

        $this->accessToken = $accessToken;
    }

    public function getCredentials()
    {
        return '';
    }

    public function getProviderKey()
    {
        return $this->providerKey;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function serialize()
    {
        return serialize(array($this->providerKey, parent::serialize()));
    }

    public function unserialize($str)
    {
        list($this->providerKey, $parentStr) = unserialize($str);
        parent::unserialize($parentStr);
    }
}
