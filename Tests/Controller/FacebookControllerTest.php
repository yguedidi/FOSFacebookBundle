<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FacebookControllerTest extends WebTestCase
{
    public function testChannelAction()
    {
        $client = static::createClient();
        $client->request('GET', '/channel.html');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $maxAge = static::$kernel->getContainer()->getParameter('yg_facebook.channel.expire');
        $this->assertEquals($maxAge, $response->getMaxAge());
    }
}