<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 05.11.16
 * Time: 20:10
 */

namespace Mittax\WsseBundle\Logger\Plugin;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mittax\WsseBundle\Logger\ILogger;
use \Monolog\Handler\StreamHandler;
use \Symfony\Bridge\Monolog\Logger;

class File implements ILogger
{
    /**
     * @var ContainerInterface
     */
    private $_container;

    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_env;


    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @param string $name
     */
    public function initHandler(string $name)
    {
        $this->_name = $name;

        $handler = new StreamHandler($this->getFile());

        $this->_logger = new Logger($this->_name);

        $this->_logger->pushHandler($handler);
    }

    /**
     * return string
     */
    public function getFile()
    {
        $logDir = $this->_container->getParameter('kernel.logs_dir');

        $env = $this->_container->getParameter('kernel.environment');

        return $logDir . '/' . $this->getName() . '_' . $env . '.log';
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        $this->_logger->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $this->_logger->alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $this->_logger->critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->_logger->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->_logger->warning($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $this->_logger->notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $this->_logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $this->_logger->debug($message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->_logger->log($message, $context);
    }
}