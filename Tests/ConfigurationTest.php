<?php

use Mittax\WsseBundle\Exception\EncoderNotFountException;
use Mittax\WsseBundle\Security\Encoder\IEncoder;
use Mittax\WsseBundle\Security\Encoder\Sha512;
use Mittax\WsseBundle\Exception\LifetimeNotFountException;
use Mittax\WsseBundle\Exception\SaltNotFountException;
use Mittax\WsseBundle\Security\Encoder\Type\TSha512;
use Mittax\WsseBundle\Tests\AbstractTest;

require_once __DIR__. '/../../../../app/autoload.php';

class ConfigurationTest extends AbstractTest
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * test existing exceptionclass
     */
    public function testSaltNotFoundExceptionManager()
    {
        $this->assertInstanceOf(SaltNotFountException::class, new SaltNotFountException());
    }

    /**
     * Testing exception
     *
     * @expectedException \Mittax\WsseBundle\Exception\SaltNotFountException
     */
    public function testthrowSaltNotFoundExceptionManager()
    {
        throw new SaltNotFountException();
    }

    /**
     * test existing exceptionclass
     */
    public function testLifetimeNotFoundExceptionManager()
    {
        $this->assertInstanceOf(LifetimeNotFountException::class, new LifetimeNotFountException());
    }


    /**
     * Testing exception
     *
     * @expectedException \Mittax\WsseBundle\Exception\EncoderNotFountException
     */
    public function testthrowEncoderNotFoundExceptionManager()
    {
        throw new EncoderNotFountException();
    }

    /**
     * test existing exceptionclass
     */
    public function testEncoderNotFoundExceptionManager()
    {
        $this->assertInstanceOf(EncoderNotFountException::class, new EncoderNotFountException());
    }

    /**
     * Testing exception
     *
     * @expectedException \Mittax\WsseBundle\Exception\LifetimeNotFountException
     */
    public function testthrowLifetimeNotFoundExceptionManager()
    {
        throw new LifetimeNotFountException();
    }


    /**
     * testing getting the salt
     */
    public function testGetSalt()
    {
        $container = $this->getContainer();

        $salt = $container->getParameter('mittax.wsse.salt');

        $this->assertNotEmpty($salt);
    }

    /**
     * testing getting the salt
     */
    public function testGetLifetime()
    {
        $container = $this->getContainer();

        $lifetime = $container->getParameter('mittax.wsse.lifetime');

        $this->assertNotEmpty($lifetime);
    }

    /**
     * testing getting encoder
     */
    public function testGetEncoder()
    {
        $container = $this->getContainer();

        $encoder = $container->getParameter('mittax.wsse.encoder');

        $this->assertNotEmpty($encoder);
    }

    /**
     * testing getting encoder
     */
    public function testGetPreventReplayAttacks()
    {
        $container = $this->getContainer();

        $encoder = $container->getParameter('mittax.wsse.preventreplayattacks');

        $this->assertNotNull($encoder);
    }

    /**
     * testing encoder instance
     */
    public function testEncoderInstance()
    {
        $container = $this->getContainer();

        $encoder = $container->getParameter('mittax.wsse.encoder');

        $encoder = new $encoder();

        $this->assertInstanceOf(get_class($encoder), new Sha512());
    }



    /**
     * Tests if encoder implements IEncoder interface
     */
    public function testEncoderImplementsInterface()
    {
        $container = $this->getContainer();

        $encoder = $container->getParameter('mittax.wsse.encoder');

        $reflectionClass = new \ReflectionClass(new $encoder());

        $this->assertTrue($reflectionClass->implementsInterface(IEncoder::class));
    }

    /**
     * Test defualt encryption sha512
     */
    public function testEncode()
    {
        $expected = 'N2Y4MDVmYmU4M2IzZWI1NDUzMDhkNGQ3MTU0N2U3MTRkN2IyM2M4NTUyZTAyNGQ2Yzc0ZTUxYzMxMzc3NzJiOGRmMTE2ZDI0YmFlYTkyZDBjN2EzOTEyZWRmNzlmMjNjODA3YTFlODI0NmVlYzY0MjlmNDRmZTVmOTM0Mzg5Zjg=';

        $container = $this->getContainer();

        $encoder = $container->getParameter('mittax.wsse.encoder');

        $salt = $container->getParameter('mittax.wsse.salt');

        $string = "1155817ba6d9474a2016-10-31T21:41:01+0000e/wAADsG9sUckbMUc140qaQ5hqY=";

        /** @var $encoder \Mittax\WsseBundle\Security\Encoder\Sha512 */
        $encoder = new $encoder();

        $config = new TSha512($salt, $string,  1000);

        $encodedDigist = $encoder->configure($config)->encode();

        $this->assertEquals($expected, $encodedDigist);
    }

}