<?php


namespace App\Payments\LiqPayRetailCrmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('modules');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('modules_settings')
            ->children()
//            //->integerNode('client_id')->end()
//            ->scalarNode('settings')->end()
//            ->end()
            ->end() // twitter
            ->end()
        ;

        return $treeBuilder;
    }

}