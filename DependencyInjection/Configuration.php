<?php

namespace Mittax\WsseBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mittax_wsse');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
                ->children()
                    ->scalarNode('salt')
                        ->defaultValue('cf83e1357eefb8bdf1542850d66d8007d620e4050b5715dc83f4a921d36ce9ce47d0d13c5d85f2b0ff8318d2877eec2f63b931bd47417a81a538327af927da3e')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('lifetime')
                        ->defaultValue(600)
                        ->isRequired()
                    ->end()
                    ->scalarNode('encoder')
                        ->defaultValue('Mittax\WsseBundle\DependencyInjection\Security\Encoders\Sha512')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->booleanNode('preventreplayattacks')
                        ->defaultValue(true)
                        ->isRequired()
                    ->end()
                    ->scalarNode('usertablename')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('passwordcolumn')
                        ->defaultValue('password')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('usernamecolumn')
                        ->defaultValue('username')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('usermanager')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
