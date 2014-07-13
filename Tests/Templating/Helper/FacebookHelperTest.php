<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\Templating\Helper;

use YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper;

class FacebookHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper::initialize
     */
    public function testInitialize()
    {
        $expected = new \stdClass();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $templating
            ->expects($this->once())
            ->method('render')
            ->with('YGFacebookBundle::initialize.html.php', array(
                'appId'   => 123,
                'async'   => true,
                'cookie'  => false,
                'culture' => 'en_US',
                'fbAsyncInit' => '',
                'logging' => true,
                'oauth' => true,
                'status'  => false,
                'channelUrl' => '/channel.html',
                'xfbml'   => false,
            ))
            ->will($this->returnValue($expected));

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getAppId'))
            ->getMock();

        $routing = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $routing
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('/channel.html'));

        $facebookMock->expects($this->once())
            ->method('getAppId')
            ->will($this->returnValue('123'));

        $helper = new FacebookHelper($templating, $facebookMock, $routing);
        $this->assertSame($expected, $helper->initialize(array('cookie' => false)));
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper::loginButton
     */
    public function testLoginButton()
    {
        $expected = new \stdClass();

        $templating = $this->getMockBuilder('Symfony\Component\Templating\DelegatingEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $templating
            ->expects($this->once())
            ->method('render')
            ->with('YGFacebookBundle::loginButton.html.php', array(
                'autologoutlink'  => 'false',
                'label'           => 'testLabel',
                'showFaces'       => 'false',
                'width'           => '',
                'maxRows'         => '1',
                'scope'           => '1,2,3',
                'registrationUrl' => '',
                'size'            => 'medium',
                'onlogin'         => ''
            ))
            ->will($this->returnValue($expected));

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getAppId'))
            ->getMock();

        $routing = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $facebookMock->expects($this->any())
            ->method('getAppId');

        $helper = new FacebookHelper($templating, $facebookMock, $routing, true, 'en_US', array(1,2,3) );
        $this->assertSame($expected, $helper->loginButton(array('label' => 'testLabel')));
    }
}
