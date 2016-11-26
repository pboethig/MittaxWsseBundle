<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 21:25
 */

namespace Mittax\WsseBundle\Client\Service\Http;

use GuzzleHttp\Exception\RequestException;

use Mittax\WsseBundle\Client\Service\IClient;
use \GuzzleHttp\Promise\PromiseInterface;
use Mittax\WsseBundle\Logger\Factory;
use Psr\Http\Message\ResponseInterface;
use \Psr\Log\LoggerInterface;

/**
 * Class GuzzleAdapter
 * @package Mittax\WsseBundle\Client\Service\Http
 */
class GuzzleAdapter implements IClient
{

    /**
     * GuzzleAdapter constructor.
     */
    public function __construct()
    {
        $this->setClient(new \GuzzleHttp\Client);
    }

    /**
     * @var Factory
     */
    private $_loggerFactory;

    /**
     * @param Factory $loggerFactory
     * @return void
     */
    public function setLoggerFactory(Factory $loggerFactory )
    {
        $this->_loggerFactory = $loggerFactory;
    }

    /**
     * @var \GuzzleHttp\Client
     */
    private $_client;

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return PromiseInterface
     */
    public function requestAsync(\Psr\Http\Message\RequestInterface  $request) : PromiseInterface
    {
        return $this->_client->sendAsync($request);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @return ResponseInterface
     */
    public function request(\Psr\Http\Message\RequestInterface  $request) : ResponseInterface
    {
        return $this->_client->send($request);
    }

    /**
     * @param object $client
     * @return void
     */
    public function setClient($client)
    {
        $this->_client = $client;
    }

    /**
     * @param $promise
     * @param callable|null $successCallback
     * @param callable|null $errorCallback
     * @return bool
     */
    public function handlePromise($promise, callable $successCallback = null, callable $errorCallback = null) : bool
    {
        /** @var $promise \GuzzleHttp\Promise\Promise */
        $promise->then(
            function (ResponseInterface $response) use ($successCallback)
            {
                if (is_callable($successCallback))
                {
                    $successCallback($response);
                }
            },
            function (RequestException $exception) use ($errorCallback)
            {
                if (is_callable($errorCallback))
                {
                    $errorCallback($exception);
                }
            }
        );

        \GuzzleHttp\Promise\unwrap([$promise]);

        return true;
    }
}