<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\DependencyInjection;

use YassineGuedidi\FacebookBundle\DependencyInjection\YGFacebookExtension;

class YGFacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\YGFacebookExtension::load
     */
    public function testLoadFailure()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $extension = $this->getMockBuilder('YassineGuedidi\\FacebookBundle\\DependencyInjection\\YGFacebookExtension')
            ->getMock();

        $extension->load(array(array()), $container);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\YGFacebookExtension::load
     */
    public function testLoadSetParameters()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag
            ->expects($this->any())
            ->method('add');

        $container
            ->expects($this->any())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        $extension = new YGFacebookExtension();
        $configs = array(
            array('class' => array('api' => 'foo')),
            array('app_id' => 'foo'),
            array('secret' => 'foo'),
            array('cookie' => 'foo'),
            array('domain' => 'foo'),
            array('logging' => 'foo'),
            array('culture' => 'foo'),
            array('channel' => array('expire' => 100)),
            array('permissions' => array('email')),
        );
        $extension->load($configs, $container);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\DependencyInjection\YGFacebookExtension::load
     */
    public function testThatCanSetContainerAlias()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects($this->once())
            ->method('setAlias')
            ->with($this->equalTo('facebook_alias'), $this->equalTo('yg_facebook.api'));

        $configs = array(
            array('class' => array('api' => 'foo')),
            array('app_id' => 'foo'),
            array('secret' => 'foo'),
            array('cookie' => 'foo'),
            array('domain' => 'foo'),
            array('logging' => 'foo'),
            array('culture' => 'foo'),
            array('channel' => array('expire' => 100)),
            array('permissions' => array('email')),
            array('alias' => 'facebook_alias')
        );
        $extension = new YGFacebookExtension();
        $extension->load($configs, $container);
    }
}
