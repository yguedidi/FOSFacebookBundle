<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

interface UserManagerInterface extends UserProviderInterface
{
    public function createUserFromUid($uid);
}
