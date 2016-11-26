<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 06.11.16
 * Time: 13:13
 */

namespace Mittax\WsseBundle\Security\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Factory
{
    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * Factory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @return TokenStorage
     */
    public function getTokenStorage()
    {
        return $this->_container->get('security.token_storage');
    }

    /**
     * @return AuthenticationProviderManager
     */
    public function getAuthenticationManager()
    {
        return $this->_container->get('security.authentication.manager');
    }
}