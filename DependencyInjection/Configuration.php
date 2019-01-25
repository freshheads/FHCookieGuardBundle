<?php
declare(strict_types=1);

namespace FH\Bundle\CookieGuardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('fh_cookie_guard');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('cookie_name')
                    ->defaultValue('cookies-accepted')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
