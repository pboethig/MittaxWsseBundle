<?php
/**
 * Created by PhpStorm.
 * User: pboethig
 * Date: 01.11.16
 * Time: 15:37
 */

namespace Mittax\WsseBundle\Tests;

use Mittax\WsseBundle\DependencyInjection\MittaxWsseExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AbstractTest extends TestCase
{
    protected $_serverUrl = 'http://localhost:8089';

    public function setUp()
    {

    }

    public function testGetContainer()
    {
        $this->assertInstanceOf(ContainerBuilder::class, $this->getContainer());
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer() : ContainerBuilder
    {
        $container = new ContainerBuilder();

        $extension = new MittaxWsseExtension();

        $container->registerExtension($extension);

        $container->loadFromExtension($extension->getAlias());

        $container->compile();

        return $container;
    }
}