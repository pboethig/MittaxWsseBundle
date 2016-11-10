<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 05.11.16
 * Time: 14:45
 */

namespace Mittax\WsseBundle\Client\Service\Http;

use GuzzleHttp\Promise\PromiseInterface;
use Mittax\WsseBundle\Client\Service\Header\Generator;
use Mittax\WsseBundle\Client\Service\Header\IHeaderService;
use Mittax\WsseBundle\Client\Service\IClient;
use Mittax\WsseBundle\Client\Service\Http\Psr7\Request as Psr7Request;
use Mittax\WsseBundle\Security\Service\Factory;
use Psr\Http\Message\ResponseInterface;
use \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
/**
 * Wsse Facade to hide wsse complexity from the developer
 *
 * Class Request
 * @package Mittax\WsseBundle\Client\Service\Http
 */
class Request
{

    /**
     * @var IClient
     */
    private $_clientAdapter;

    /**
     * @var IHeaderService
     */
    private $_headerService;

    /**
     * @var Generator
     */
    private $_generator;

    /**
     * @var array
     */
    private $_wsseHeaderOptions;

    /**
     * @var TokenStorage
     */
    private $_securityTokenStorage;

    /**
     * @var AuthenticationProviderManager
     */
    private $_authenticationManager;

    /**
     * Request constructor.
     * @param IClient $clientAdapter
     * @param Generator $generator
     * @param Factory $securityFactory
     */
    public function __construct(IClient $clientAdapter, Generator $generator, Factory $securityFactory)
    {
        $this->_clientAdapter = $clientAdapter;

        $this->_generator = $generator;

        $this->_headerService = $generator->getHeaderService();

        $this->_securityTokenStorage = $securityFactory->getTokenStorage();

        $this->_authenticationManager = $securityFactory->getAuthenticationManager();
    }

    /**
     * @return array
     */
    public function getWsseHeaderOptions()
    {
        return $this->_wsseHeaderOptions;
    }

    /**
     * Sets default wsse header options.
     *
     * @param array $wsseHeaderOptions
     */
    public function setWsseHeaderOptions(array $wsseHeaderOptions)
    {
        $this->_wsseHeaderOptions =  $wsseHeaderOptions;
    }

    /**
     * @param string $username
     * @return array
     */
    public function getWsseHeaderRequestOtionsByUsername(string $username) : Array
    {
        return ['X-WSSE' => $this->_generator->getHeaderStringByUsername($username)];
    }

    /**
     * Sends request "asynchroniosly"
     *
     * @param string $method
     * @param $uri
     * @param array $options
     * @return mixed
     */
    public function async($method = 'GET', $uri, array $options = [], string $body = null)
    {
        $request = $this->getRequest($method, $uri, $options, $body);

        $promise = $this->_clientAdapter->requestAsync($request);

        return $promise;
    }

    /**
     * Sends request "syncron"
     *
     * @param string $method
     * @param $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function request($method = 'GET', $uri, array $options = [], string $body = null) : ResponseInterface
    {
        $request = $this->getRequest($method, $uri, $options, $body);

        $promise = $this->_clientAdapter->request($request);

        return $promise;
    }

    /**
     * @param string $method
     * @param $uri
     * @param array $options
     * @param string|null $body
     * @return Psr7Request
     */
    public function getRequest($method = 'GET', $uri, array $options = [], string $body = null) : Psr7Request
    {
        if ($body)
        {
            $request = new Psr7Request($method, $uri,  $options, $body);
        }
        else
        {
            $request = new Psr7Request($method, $uri,  $options);
        }

        return $request;
    }

    /**
     * @param PromiseInterface $promise
     * @param callable|null $successCallback
     * @param callable|null $errorCallback
     */
    public function handlePromise(PromiseInterface $promise, callable $successCallback = null, callable $errorCallback = null)
    {
        $this->_clientAdapter->handlePromise($promise, $successCallback, $errorCallback);
    }
}