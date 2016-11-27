<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 15:20
 */

namespace Mittax\WsseBundle\Tests;

use Mittax\WsseBundle\Security\Encoder\Sha512;
use Mittax\WsseBundle\Security\Header\WsseHeader;

/**
 * Class WsseHeaderTest
 * @package Mittax\WsseBundle\Tests\Security
 */
class WsseHeaderTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private $_password = 'a passsword';

    /**
     * @var string
     */
    private $_salt = 'asalthere';

    /**
     * @var string
     */
    private $_username = 'mittax';

    /**
     * @var \Mittax\WsseBundle\Security\Header\WsseHeader
     */
    private $_sha512Header;

    /**
     * @var Sha512
     */
    private $_sha512Encoder;


    public function setUp()
    {
        parent::setUp();

        $this->_wsseSha512HeaderService = $this->container->get('mittax_wsse.client.service.header.wsssha512');

        $this->_sha512Header = $this->_wsseSha512HeaderService->getHeader($this->_username, $this->_password, $this->_salt);

        $this->_sha512Encoder = $this->_sha512Header->getEncoder();
    }

    /**
     * Test the sha512 encoder instance
     */
    public function testSha512EncoderInstance()
    {
        $this->assertInstanceOf(Sha512::class, $this->_sha512Encoder);
    }

    /**
     * Test creating WsseHeader
     */
    public function testHeaderInstance()
    {
        $this->assertNotNull($this->_sha512Header);

        $this->assertInstanceOf(WsseHeader::class, $this->_sha512Header);
    }


    /**
     * Test creating WsseHeader
     */
    public function testHeaderStringInstance()
    {
        $headerString = $this->_sha512Header->toString();

        $this->assertNotEmpty($headerString);

        $this->assertContains('Username', $headerString);

        $this->assertContains('Password', $headerString);

        $this->assertContains('Nonce', $headerString);

        $this->assertContains('PasswordDigest', $headerString);

        $this->assertContains('Created', $headerString);
    }
}