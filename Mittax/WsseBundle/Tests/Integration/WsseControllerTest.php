<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 10.11.16
 * Time: 20:55
 */

namespace Mittax\WsseBundle\Tests\Client\Service\Integration;


use Mittax\WsseBundle\Tests\AbstractKernelTestCase;

class WsseControllerTest extends AbstractKernelTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_ApiMethod_GetByUserName()
    {
        $wsseHeader = $this->_requestClient->getWsseHeaderRequestOtionsByUsername($this->_adminuser);

        $username = 'mittax2';

        $response = $this->_requestClient->request('GET', $this->_serverUrl . '/wsse/'. $username, $wsseHeader);

        $this->assertEquals(200, $response->getStatusCode());
    }
}