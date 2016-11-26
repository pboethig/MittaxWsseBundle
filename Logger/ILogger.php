<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 05.11.16
 * Time: 20:56
 */

namespace Mittax\WsseBundle\Logger;


use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implement extended Psr LoggerInterface
 * Is able to register custom handler like kibana, logstash
 *
 * Interface ILogger
 * @package Mittax\WsseBundle\Logger
 */
interface ILogger extends LoggerInterface
{
    /**
     * Initializes the loghandler
     *
     * @param $name
     * @return mixed
     */
    public function initHandler(string $name);

    /**
     * Set Application / BundleContainer
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);
}