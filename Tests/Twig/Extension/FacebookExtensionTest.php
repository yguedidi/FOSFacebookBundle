<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\Twig\Extension;

use YassineGuedidi\FacebookBundle\Twig\Extension\FacebookExtension;
use YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper;

class FacebookExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers YassineGuedidi\FacebookBundle\Twig\Extension\FacebookExtension::getName
     */
    public function testGetName()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $extension = new FacebookExtension($containerMock);
        $this->assertSame('facebook', $extension->getName());
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Twig\Extension\FacebookExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $extension = new FacebookExtension($containerMock);
        $functions = $extension->getFunctions();
        $this->assertInstanceOf('\Twig_Function_Method', $functions['facebook_initialize']);
        $this->assertInstanceOf('\Twig_Function_Method', $functions['facebook_login_button']);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Twig\Extension\FacebookExtension::renderInitialize
     */
    public function testRenderInitialize()
    {
        $helperMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('initialize')
            ->will($this->returnValue('returnedValue'));
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock->expects($this->once())
            ->method('get')
            ->with('yg_facebook.helper')
            ->will($this->returnValue($helperMock));

        $extension = new FacebookExtension($containerMock);
        $this->assertSame('returnedValue', $extension->renderInitialize());
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Twig\Extension\FacebookExtension::renderloginButton
     */
    public function testRenderLoginButton()
    {
        $helperMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('loginButton')
            ->will($this->returnValue('returnedValueLogin'));
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock->expects($this->once())
            ->method('get')
            ->with('yg_facebook.helper')
            ->will($this->returnValue($helperMock));

        $extension = new FacebookExtension($containerMock);
        $this->assertSame('returnedValueLogin', $extension->renderLoginButton());
    }
}
