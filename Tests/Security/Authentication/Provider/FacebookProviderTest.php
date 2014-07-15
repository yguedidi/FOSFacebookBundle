<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\Security\Authentication\Provider;

use YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken;

use YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class FacebookProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThatUserCheckerCannotBeNullWhenUserProviderIsNotNull()
    {
        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->getMock();
        new FacebookProvider('main', $facebookMock, $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface'));
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     */
    public function testThatCannotAuthenticateWhenTokenIsNotFacebookToken()
    {
        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->getMock();
        $facebookProvider = new FacebookProvider('main', $facebookMock);
        $this->assertNull($facebookProvider->authenticate($this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')));
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::supports
     */
    public function testThatCannotAuthenticateWhenTokenFromOtherFirewall()
    {
        $providerKeyForProvider = 'main';
        $providerKeyForToken    = 'connect';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->getMock();
        $facebookProvider = new FacebookProvider($providerKeyForProvider, $facebookMock);

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getProviderKey'), array($providerKeyForToken));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKeyForToken));

        $this->assertFalse($facebookProvider->supports($tokenMock));
        $this->assertNull($facebookProvider->authenticate($tokenMock));
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::supports
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::createAuthenticatedToken
     */
    public function testThatCanAuthenticateUserWithoutUserProvider()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock);

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getAttributes', 'getProviderKey'), array($providerKey));
        $tokenMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $this->assertTrue($facebookProvider->supports($tokenMock));
        $this->assertEquals('123', $facebookProvider->authenticate($tokenMock)->getUser());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatCannotAuthenticateWhenUserProviderThrowsAuthenticationException()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->throwException(new AuthenticationException('test')));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getProviderKey'), array($providerKey));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatCannotAuthenticateWhenUserProviderDoesNotReturnUserInterface()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->returnValue('234'));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getProviderKey'), array($providerKey));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatCannotAuthenticateWhenCannotRetrieveFacebookUserFromSession()
    {
        $providerKey = 'main';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue(false));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getProviderKey'), array($providerKey));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::createAuthenticatedToken
     */
    public function testThatCanAutenticateUsingUserProvider()
    {
        $providerKey = 'main';

        $userMock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $userMock->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue('l3l0'));
        $userMock->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array()));

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->returnValue($userMock));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userCheckerMock->expects($this->once())
            ->method('checkPostAuth');

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getAttributes', 'getProviderKey'), array($providerKey));
        $tokenMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $this->assertEquals('l3l0', $facebookProvider->authenticate($tokenMock)->getUsername());
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testThatAccessTokenIsSetToFacebookSessionPersistenceWithAccessTokenFromFacebookToken()
    {
        $providerKey = 'main';
        $accessToken = 'AbCd';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('setAccessToken','getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
          ->method('setAccessToken')
          ->with($accessToken);
        $facebookMock->expects($this->once())
            ->method('getUser');

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getProviderKey','getAccessToken'), array($providerKey,'',array(),$accessToken));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));
        $tokenMock->expects($this->any())
             ->method('getAccessToken')
             ->will($this->returnValue($accessToken));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $facebookProvider->authenticate($tokenMock);
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::createAuthenticatedToken
     */
    public function testThatAccessTokenIsSetToNewFacebookTokenWhenAuthenticateWithUserProvider()
    {
        $providerKey = 'main';
        $accessToken = 'AbCd';

        $userMock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $userMock->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue(array()));

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();

        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $userProviderMock = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $userProviderMock->expects($this->once())
            ->method('loadUserByUsername')
            ->with('123')
            ->will($this->returnValue($userMock));

        $userCheckerMock = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $userCheckerMock->expects($this->once())
            ->method('checkPostAuth');

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getAttributes', 'getProviderKey'), array($providerKey,'',array(),$accessToken));
        $tokenMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock, $userProviderMock, $userCheckerMock);
        $this->assertEquals($accessToken, $facebookProvider->authenticate($tokenMock)->getAccessToken());
    }

    /**
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::authenticate
     * @covers YassineGuedidi\FacebookBundle\Security\Authentication\Provider\FacebookProvider::createAuthenticatedToken
     */
    public function testThatAccessTokenIsSetToNewFacebookTokenWhenAuthenticateWithoutUserProvider()
    {
        $providerKey = 'main';
        $accessToken = 'AbCd';

        $facebookMock = $this->getMockBuilder('YassineGuedidi\FacebookBundle\Facebook\FacebookSessionPersistence')
            ->disableOriginalConstructor()
            ->setMethods(array('getUser'))
            ->getMock();
        $facebookMock->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue('123'));

        $facebookProvider = new FacebookProvider($providerKey, $facebookMock);

        $tokenMock = $this->getMock('YassineGuedidi\FacebookBundle\Security\Authentication\Token\FacebookToken', array('getAttributes', 'getProviderKey'), array($providerKey,'',array(),$accessToken));
        $tokenMock->expects($this->once())
            ->method('getAttributes')
            ->will($this->returnValue(array()));
        $tokenMock->expects($this->any())
            ->method('getProviderKey')
            ->will($this->returnValue($providerKey));

        $this->assertEquals($accessToken, $facebookProvider->authenticate($tokenMock)->getAccessToken());

    }
}
