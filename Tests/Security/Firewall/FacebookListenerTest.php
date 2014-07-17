<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\Security\Firewall\FacebookListener;

use YassineGuedidi\FacebookBundle\Security\Firewall\FacebookListener;

class FacebookListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Firewall\FacebookListener::attemptAuthentication
     */
    public function testThatCantAttemptAuthenticationWithoutFacebookQueryParameter()
    {
        $authenticationManagerMock = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManagerMock->expects($this->never())
            ->method('authenticate');

        $listener = new FacebookListener(
            $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'),
            $authenticationManagerMock,
            $this->getMock('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface'),
            $this->getMock('Symfony\Component\Security\Http\HttpUtils'),
            'providerKey',
            $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface'),
            $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface')
        );
        $this->assertNull($listener->handle($this->getResponseEvent()));
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Firewall\FacebookListener::attemptAuthentication
     */
    public function testThatCanAttemptAuthenticationWithFacebook()
    {
        $listener = new FacebookListener(
            $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface'),
            $this->getAuthenticationManager(),
            $this->getMock('Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface'),
            $this->getMock('Symfony\Component\Security\Http\HttpUtils'),
            'providerKey',
            $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface'),
            $this->getMock('Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface')
        );
        $listener->handle($this->getResponseEvent(array('state' => 'foo', 'code' => 'bar')));
    }

    /**
     * @return Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface
     */
    private function getAuthenticationManager()
    {
        $authenticationManagerMock = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $authenticationManagerMock->expects($this->once())
            ->method('authenticate')
            ->with($this->isInstanceOf('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken'));

        return $authenticationManagerMock;
    }

    /**
     * @return Symfony\Component\HttpKernel\Event\GetResponseEvent
     */
    private function getResponseEvent(array $query = array())
    {
        $responseEventMock = $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array('getRequest'), array(), '', false);
        $responseEventMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->getRequest($query)));

        return $responseEventMock;
    }

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    private function getRequest(array $query = array())
    {
        $requestMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalClone()
            ->setMethods(array('hasSession', 'hasPreviousSession'))
            ->setConstructorArgs(array($query))
            ->getMock();
        $requestMock->expects($this->any())
            ->method('hasSession')
            ->will($this->returnValue('true'));
        $requestMock->expects($this->any())
            ->method('hasPreviousSession')
            ->will($this->returnValue('true'));

        return $requestMock;
    }

}
