<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle;

use YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class YGFacebookBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new FacebookFactory());
    }
}
