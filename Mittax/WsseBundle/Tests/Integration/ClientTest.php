<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 21:15
 */

namespace Mittax\WsseBundle\Tests\Client\Service\Integration;

use Mittax\WsseBundle\Logger\Factory;
use Mittax\WsseBundle\Logger\ILogger;
use Mittax\WsseBundle\Tests\AbstractKernelTestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ClientTest
 * @package Mittax\WsseBundle\Tests\Client\Service
 */
class ClientTest extends AbstractKernelTestCase
{
    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Create usertable to test against live datas.
     */
    public function testCreateuserTableInTestDatabase()
    {
        $this->initDatabase(__DIR__ . '/Fixures', 'fos_user.sql');
    }

    public function testLoggerFactoryInstance()
    {
        $this->assertInstanceOf(Factory::class, $this->_loggerFactory);
    }

    /**
     * Test fileLogger plugin
     */
    public function testFileLoggerPlugin()
    {
        $logger = $this->_loggerFactory->getLogger('testcalls');

        $this->assertInstanceOf(ILogger::class, $logger);
    }


    /**
     * Test fileLogger plugin
     */
    public function testGetWsseHeaderRequestOtionsByUsername()
    {
        $headerOptions = $this->_requestClient->getWsseHeaderRequestOtionsByUsername($this->_adminuser);

        $this->assertArrayHasKey('X-WSSE',  $headerOptions);
    }

    public function _testGetHeader()
    {
        $ĥeaderString = $this->getHeaderStringByUsername('mittax3');

        $this->assertContains('Username', $ĥeaderString);

        $this->assertContains('Password', $ĥeaderString);

        $this->assertContains('Created', $ĥeaderString);

        $this->assertContains('Nonce', $ĥeaderString);
    }

    /**
     * Test defaultoptions for X-WSSE header index
     */
    public function testDefaultOptionsForWSSEHeaderIndex()
    {
        $this->_requestClient->setWsseHeaderOptions($this->_wsseHeaderOptions);

        $this->assertEquals($this->_wsseHeaderOptions, $this->_requestClient->getWsseHeaderOptions());
    }

    /**
     * Test defaultoptions for X-WSSE header values
     */
    public function testDefaultOptionsForWSSEHeaderValues()
    {
        $this->_requestClient->setWsseHeaderOptions($this->_wsseHeaderOptions);

        $this->assertContains('Username', $this->_requestClient->getWsseHeaderOptions()['X-WSSE']);

        $this->assertContains('Password', $this->_requestClient->getWsseHeaderOptions()['X-WSSE']);

        $this->assertContains('Created', $this->_requestClient->getWsseHeaderOptions()['X-WSSE']);

        $this->assertContains('Nonce', $this->_requestClient->getWsseHeaderOptions()['X-WSSE']);
    }

    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     * @expectedExceptionMessage Unauthorized
     */
    public function testIncorrectHeaderLeadsToA401UnauthorizedHeader()
    {
        $path = '/emotico';

        $promise = $this->_requestClient->async('GET', $this->_serverUrl . $path, ['X-Wsse']);

        $this->_requestClient->handlePromise($promise, $this->_successCallback, $this->_errorCallback);
    }

    /**
     * Test a response on a list api method and checks json decoded objectlist for a available items
     */
    public function test200ResponsesAsync()
    {
        $path = '/emotico';

        $logger = $this->_logger;

        $filename = 'emotico.json';

        $filename = __DIR__ . '/AsyncResponses/' .$filename;

        $successCallback = function (ResponseInterface $response) use ($logger, $filename)
        {
            file_put_contents($filename, (string)$response->getBody());

            $logger->info('Status: ' . $response->getStatusCode());
        };

        $promise = $this->_requestClient->async('GET', $this->_serverUrl . $path, $this->_wsseHeaderOptions);

        $this->_requestClient->handlePromise($promise, $successCallback, $this->_errorCallback);

        $content = file_get_contents($filename);

        $this->assertNotEmpty($content);

        $objectList = json_decode($content);

        $this->assertGreaterThan(0, count($objectList));

        $this->assertObjectHasAttribute('id', $objectList[0]);

        $this->assertGreaterThan(0, $objectList[0]->id);
    }


    /**
     * Test a response on a list api method and checks json decoded objectlist for a available items
     */
    public function test200ResponsesSyncronosly()
    {
        $path = '/emotico';

        $response = $this->_requestClient->request('GET', $this->_serverUrl . $path, $this->_wsseHeaderOptions);

        $json = (string)$response->getBody();

        $this->assertNotEmpty( $json);

        $objectList = json_decode($json);

        $this->assertGreaterThan(0, count($objectList));

        $this->assertObjectHasAttribute('id', $objectList[0]);

        $this->assertGreaterThan(0, $objectList[0]->id);
    }

    /**
     * Tests if the header is predictable to status 200. If it doesnt throw a psr 7 requestexception
     */
    public function testCorrectHeaderLeadsNotToForbiddenOrUnauthorizedHeader()
    {
        $uri = $this->_serverUrl . '/emotico';

        $promise = $this->_requestClient->async('GET',  $uri, $this->_wsseHeaderOptions);

        $this->_requestClient->handlePromise($promise, $this->_successCallback, $this->_errorCallback);
    }

    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     * @expectedExceptionMessage Forbidden
     */
    public function testCorrectHeaderTo403ForbiddenHeader()
    {
        $path = '/emotico';

        $manipulatedHeader = $this->_wsseHeaderOptions;

        $manipulatedHeader['X-WSSE'] = str_replace('PasswordDigest="','PasswordDigest="fakepassword',$manipulatedHeader['X-WSSE']);

        $promise = $this->_requestClient->async('GET', $this->_serverUrl . $path, $manipulatedHeader);

        $this->_requestClient->handlePromise($promise, $this->_successCallback, $this->_errorCallback);
    }
    
    
    
}