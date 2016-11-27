<?php

namespace Mittax\WsseBundle\DependencyInjection;

use Mittax\WsseBundle\Exception\WsseConfigNotFoundException;
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

        $config['mittax_wsse'] = new Configuration();
        $config = $this->processConfiguration($config['mittax_wsse'], $configs);

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
        /**
         * Get the parameters from app/config/parameters
         */
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../../../app/config/parameters.yml'));

        if (!isset($config['mittax_wsse']))
        {
            throw new WsseConfigNotFoundException('No mittax_wsse configuration found in app/config/parameters.yml. Forgot to add it?. @see documentation');
        }
        
        foreach ($config as $key => $configuration)
        {
            $container->setParameter('mittax.wsse.salt', $config['mittax_wsse']['salt']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.lifetime', $config['mittax_wsse']['lifetime']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.encoder', $config['mittax_wsse']['encoder']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.preventreplayattacks', $config['mittax_wsse']['preventreplayattacks']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.passwordcolumn', $config['mittax_wsse']['passwordcolumn']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.usertablename', $config['mittax_wsse']['usertablename']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.usernamecolumn', $config['mittax_wsse']['usernamecolumn']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.usermanager', $config['mittax_wsse']['usermanager']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.integrationtestsusername', $config['mittax_wsse']['integrationtestsusername']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);

            $container->setParameter('mittax.wsse.integrationtestsserverurl', $config['mittax_wsse']['integrationtestsserverurl']);
            $container->prependExtensionConfig($key, $config['mittax_wsse']);
       }
    }


}