<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\Security\EntryPoint;

use YassineGuedidi\FacebookBundle\Security\EntryPoint\FacebookAuthenticationEntryPoint;

class FacebookAuthenticationEntryPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers YassineGuedidi\FacebookBundle\Security\EntryPoint\FacebookAuthenticationEntryPoint::start
     */
    public function testThatRedirectResponseWithFacebookLoginUrlIsCreated()
    {
        $requestMock = $this->getMock('Symfony\Component\HttpFoundation\Request', array('getUri'));
        $requestMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('http://localhost/index'));

        $options = array('check_path' => '/index', 'redirect_to_facebook_login' => true);
        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getLoginUrl'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getLoginUrl')
            ->with($this->equalTo(array(
                'display' => 'page',
                'scope' => 'email,user_website',
                'redirect_uri' => 'http://localhost/index'
            )))
            ->will($this->returnValue('http://localhost/facebook-redirect/index'));

        $facebookAuthentication = new FacebookAuthenticationEntryPoint($facebookMock, $options, array('email', 'user_website'));
        $response = $facebookAuthentication->start($requestMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response, 'RedirectResponse is returned');
        $this->assertEquals($response->headers->get('location'), 'http://localhost/facebook-redirect/index', 'RedirectResponse has defined expected location');
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\EntryPoint\FacebookAuthenticationEntryPoint::start
     */
    public function testThatRedirectResponseWithoutFacebookLoginUrlIsCreated()
    {
        $requestMock = $this->getMock('Symfony\Component\HttpFoundation\Request', array('getUri'));
        $requestMock->expects($this->never())
            ->method('getUri');

        $options = array(
            'check_path'                    => '/index',
            'login_path'                    => '/login',
            'redirect_to_facebook_login'    => false
        );
        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getLoginUrl'))
            ->getMock();
        $facebookMock->expects($this->never())
            ->method('getLoginUrl');

        $facebookAuthentication = new FacebookAuthenticationEntryPoint($facebookMock, $options, array('email', 'user_website'));
        $response = $facebookAuthentication->start($requestMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response, 'RedirectResponse is returned');
        $this->assertEquals($response->headers->get('location'), '/login', 'RedirectResponse has defined expected location');
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\EntryPoint\FacebookAuthenticationEntryPoint::start
     */
    public function testThatRedirectionToFacebookLoginUrlIsCreated()
    {
        $requestMock = $this->getMock('Symfony\Component\HttpFoundation\Request', array('getUri'));

        $options = array(
            'check_path'                    => '/index',
            'server_url'                    => 'http://server.url',
            'app_url'                       => 'http://app.url',
            'redirect_to_facebook_login'    => true
        );
        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getLoginUrl'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getLoginUrl')
            ->will($this->returnValue('http://localhost/facebook-redirect/index'));

        $facebookAuthentication = new FacebookAuthenticationEntryPoint($facebookMock, $options, array());
        $response = $facebookAuthentication->start($requestMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response, 'Response is returned');
        $this->assertRegExp('/location\.href="http:\/\/localhost\/facebook-redirect\/index/', $response->getContent(), 'Javascript redirection is in response');
    }
}
