<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 05.11.16
 * Time: 18:27
 */

namespace Mittax\WsseBundle\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Factory
 * @package Mittax\WsseBundle\Client\Service\Logger
 */
class Factory
{
    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->_container = $container;
    }


    /**
     * @return LoggerInterface
     */
    public function getLogger($name = 'restcalls', $pluginClass ='mittax_wsse.logger.plugin.file')
    {
        /** @var $logger ILogger  */
        $logger = $this->_container->get($pluginClass);

        $logger->initHandler($name);

        return  $logger;
    }
}