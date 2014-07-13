<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class FacebookController extends ContainerAware
{
    /**
     * public function channelAction()
     *
     * This function mimics the channel.html file suggested by facebook.
     *
     * References :
     * https://developers.facebook.com/docs/reference/javascript/
     *
     * @version         1.0
     *
     * @author          Antoine Durieux
     *
     * @return Response
     */
    public function channelAction()
    {
        // Retrieve parameters from the container.
        $culture = $this->container->getParameter('yg_facebook.culture');
        $cacheExpire = $this->container->getParameter('yg_facebook.channel.expire');

        // Compute expiration date.
        $date = new \DateTime();
        $date->modify('+'.$cacheExpire.' seconds');

        // Generate new response, and set parameters recommended by Facebook.
        $response = new Response();
        $response->headers->set("Pragma", "public");
        $response->setMaxAge($cacheExpire);
        $response->setExpires($date);
        $response->setContent('<script src="//connect.facebook.net/'.$culture.'/all.js"></script>');

        return $response;
    }

}
