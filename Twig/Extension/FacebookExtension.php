<?php

/*
 * This file is part of the YGFacebookBundle package.
 *
 * (c) Yassine Guedidi <yassine@guedidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YassineGuedidi\FacebookBundle\Twig\Extension;

use YassineGuedidi\FacebookBundle\Templating\Helper\FacebookHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FacebookExtension extends \Twig_Extension
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'facebook_initialize' => new \Twig_Function_Method($this, 'renderInitialize', array('is_safe' => array('html'))),
            'facebook_login_button' => new \Twig_Function_Method($this, 'renderLoginButton', array('is_safe' => array('html'))),
            'facebook_logout_url' => new \Twig_Function_Method($this, 'renderLogoutUrl', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'facebook';
    }

    /**
     * @see FacebookHelper::initialize()
     */
    public function renderInitialize($parameters = array(), $name = null)
    {
        return $this->container->get('yg_facebook.helper')->initialize($parameters, $name ?: 'YGFacebookBundle::initialize.html.twig');
    }

    /**
     * @see FacebookHelper::loginButton()
     */
    public function renderLoginButton($parameters = array(), $name = null)
    {
        return $this->container->get('yg_facebook.helper')->loginButton($parameters, $name ?: 'YGFacebookBundle::loginButton.html.twig');
    }

    /**
     * @see FacebookHelper::logoutUrl()
     */
    public function renderLogoutUrl($parameters = array(), $name = null)
    {
        return $this->container->get('yg_facebook.helper')->logoutUrl($parameters);
    }
}
