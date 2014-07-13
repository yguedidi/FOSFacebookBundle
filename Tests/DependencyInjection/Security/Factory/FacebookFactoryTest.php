<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\DependencyInjection\Security\Factory;

use YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory;

class FacebookFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory
     */
    private $factory = null;

    public function setUp()
    {
        $this->factory = new FacebookFactory();
    }

    public function testThatCanGetPosition()
    {
        $this->assertEquals('pre_auth', $this->factory->getPosition());
    }

    public function testThatCanGetKey()
    {
        $this->assertEquals('yg_facebook', $this->factory->getKey());
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory::createAuthProvider
     */
    public function testThatCreateUserAuthProviderWhenDefinedInConfig()
    {
        $idsArray = $this->facebookFactoryCreate(array('provider' => true, 'remember_me' => false, 'create_user_if_not_exists' => false));
        $this->assertEquals('yg_facebook.auth.l3l0', $idsArray[0]);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory::createAuthProvider
     */
    public function testThatCreateUserAuthProviderEvenWhenNotDefinedInConfig()
    {
        $idsArray = $this->facebookFactoryCreate(array('remember_me' => false));
        $this->assertEquals('yg_facebook.auth.l3l0', $idsArray[0]);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory::createAuthProvider
     */
    public function testThatCreateDifferentUserAuthProviderForDifferentFirewalls()
    {
        $idsArray = $this->facebookFactoryCreate(array('remember_me' => false));
        $this->assertEquals('yg_facebook.auth.l3l0', $idsArray[0]);

        $idsArray = $this->facebookFactoryCreate(array('remember_me' => false), 'main');
        $this->assertEquals('yg_facebook.auth.main', $idsArray[0]);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory::createEntryPoint
     */
    public function testThatCreateEntryPoint()
    {
        $idsArray = $this->facebookFactoryCreate(array('remember_me' => false));
        $this->assertEquals('yg_facebook.security.authentication.entry_point.l3l0', $idsArray[2]);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\Security\Factory\FacebookFactory::getListenerId
     */
    public function testThatListenerForListenerId()
    {
        $idsArray = $this->facebookFactoryCreate(array('remember_me' => false));
        $this->assertEquals('yg_facebook.security.authentication.listener.l3l0', $idsArray[1]);
    }

    /**
     * @param  array $config
     * @return array
     */
    private function facebookFactoryCreate($config = array(), $id = 'l3l0')
    {
        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition', array('addArgument', 'replaceArgument'));
        $definition->expects($this->any())
            ->method('replaceArgument')
            ->will($this->returnValue($definition));
        $definition->expects($this->any())
            ->method('addArgument')
            ->will($this->returnValue($definition));
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder', array('setDefinition'));
        $container->expects($this->any())
            ->method('setDefinition')
            ->will($this->returnValue($definition));

        return $this->factory->create($container, $id, $config, 'l3l0.user.provider', null);
    }
}
