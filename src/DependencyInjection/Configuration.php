<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('polidog_hypermedia');
        $rootNode
            ->children()
                ->booleanNode('hal_content_type')->defaultFalse()->end()
                ->booleanNode('enable_absolute_url')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
