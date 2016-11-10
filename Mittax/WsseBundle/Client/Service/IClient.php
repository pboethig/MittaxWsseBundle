<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 21:26
 */

namespace Mittax\WsseBundle\Client\Service;

use Mittax\WsseBundle\Logger\Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface IClient
 * @package Mittax\WsseBundle\Client\Service
 */
interface IClient
{
    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function requestAsync(RequestInterface $request);

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function request(RequestInterface  $request) : ResponseInterface;
    /**
     * @param object $client
     * @return IClient
     */
    public function setClient($client);

    /**
     * @param $promise
     * @param callable|null $callback
     * @param callable|null $errorCallback
     * @return mixed
     */
    public function handlePromise($promise, callable $callback = null, callable $errorCallback = null);

    /**
     * @param Factory $logger
     * @return mixed
     */
    public function setLoggerFactory( Factory $logger );



}