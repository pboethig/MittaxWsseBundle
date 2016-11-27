<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 06.11.16
 * Time: 21:45
 */

namespace Mittax\WsseBundle\Tests;
require_once __DIR__ . '/../../../../app/bootstrap.php.cache';

require_once __DIR__ . '/../../../../app/AppKernel.php';


use Doctrine\ORM\EntityManager;
use Mittax\WsseBundle\Client\Service\Header\Generator;
use Mittax\WsseBundle\Client\Service\Header\WsseSha512;
use Mittax\WsseBundle\Logger\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class AbstractKernelTestCase
 * @package Mittax\WsseBundle\Tests
 */
class AbstractKernelTestCase extends KernelTestCase
{

    /**
     * @var string
     */
    protected $_serverUrl;

    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var WsseSha512
     */
    protected $_wsseSha512HeaderService;

    /**
     * @var \Mittax\WsseBundle\Client\Service\Http\Request
     */
    protected $_requestClient;

    /**
     * @var Factory
     */
    protected $_loggerFactory;

    /**
     * @var AuthenticationProviderManager
     */
    protected $_authenticationManager;

    /**
     * @var TokenStorage
     */
    protected $_tokenStorage;

    /**
     * @var EntityManager
     */
    protected $_entityManager;


    /**
     * @var Application
     */
    private $_application;

    /**
     * @var KernelInterface
     */
    protected $_kernel;

    /**
     * @var Generator
     */
    protected $_headerGenerator;

    /**
     * @var string
     */
    protected $_adminuser;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var callable
     */
    protected $_successCallback;

    /**
     * @var callable
     */
    protected $_errorCallback;

    /**
     * @var array
     */
    protected $_wsseHeaderOptions;


    /**
     * @param string $bundle
     */
    public function setBundle(string $bundle)
    {
        $this->_bundle = $bundle;
    }

    public function setUp()
    {
        parent::setUp();

        $this->_kernel = static::createKernel();
        $this->_kernel->boot();

        $this->container = $this->_kernel->getContainer();

        $this->_entityManager = $this->container->get('doctrine');

        $this->_adminuser = $this->container->getParameter('mittax.wsse.integrationtestsusername');

        $this->_wsseSha512HeaderService = $this->container->get('mittax_wsse.client.service.header.wsssha512');

        $this->_requestClient = $this->container->get('mittax_wsse.client.service.http.request');

        $this->_loggerFactory = $this->container->get('mittax_wsse.logger.factory');

        $this->_authenticationManager = $this->container->get('mittax_wsse.security.service.factory')->getAuthenticationManager();

        $this->_tokenStorage = $this->container->get('mittax_wsse.security.service.factory')->getTokenStorage();

        $this->_headerGenerator = $this->container->get('mittax_wsse.client.service.header.generator');

        $this->_wsseHeaderOptions = ['X-WSSE'=>$this->getHeaderStringByUsername($this->_adminuser)];

        $logger = $this->_logger;

        $this->_successCallback = function (ResponseInterface $response) use ($logger)
        {
            $logger->info('Status: ' . $response->getStatusCode());
        };

        $this->_errorCallback = function (\HttpRequestException $exception) use ($logger)
        {
            $logger->warning(PHP_EOL . PHP_EOL .' Code: '. $exception->getCode() .' Message: '.$exception->getMessage(). ' in class: ' . __CLASS__ . '  in Line:' . __LINE__);
        };


        $this->_serverUrl = $this->container->getParameter('mittax.wsse.integrationtestsserverurl');

        $this->_initApplication();
    }

    /**
     * @param $username
     * @return string
     */
    public function getHeaderStringByUsername($username) : string
    {
        return $this->_headerGenerator->{__FUNCTION__}($username);
    }

    /**
     * @param $application
     * @param $command
     * @param array $options
     */
    public function executeCommand(Application $application, $command, Array $options = [])
    {
        $options["--quiet"] = true;

        $options["--env"] = 'test';

        $options = array_merge($options, array('command' => $command));

        $application->run(new StringInput($command));
    }

    /**
     * @param $dir
     * @param $sqlfile
     */
    public function initDatabase($dir, $sqlfile)
    {
        $finder = new Finder();

        $finder->files()->in($dir);

        $finder->name($sqlfile);

        foreach( $finder as $file )
        {
            $command = 'doctrine:database:import '. $file;

            $this->executeCommand($this->_application, $command);
        }
    }

    private function _initApplication()
    {
        $kernel = new \AppKernel('test', true);

        $kernel->boot();

        $this->_application = new Application($kernel);

        $this->_application->setAutoExit(false);
    }
}