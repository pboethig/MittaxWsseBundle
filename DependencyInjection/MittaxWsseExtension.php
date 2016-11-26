<?php

namespace Mittax\WsseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MittaxWsseExtension extends Extension implements PrependExtensionInterface, CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // ... do something during the compilation
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Prepend needed configparams to the loading process.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/config.yml'));

        foreach ($config as $key => $configuration) {

            $container->setParameter('mittax.wsse.salt', $configuration['salt']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.lifetime', $configuration['lifetime']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.encoder', $configuration['encoder']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.preventreplayattacks', $configuration['preventreplayattacks']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.passwordcolumn', $configuration['passwordcolumn']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.usertablename', $configuration['usertablename']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.usernamecolumn', $configuration['usernamecolumn']);
            $container->prependExtensionConfig($key, $configuration);

            $container->setParameter('mittax.wsse.usermanager', $configuration['usermanager']);
            $container->prependExtensionConfig($key, $configuration);
        }
    }

    
}