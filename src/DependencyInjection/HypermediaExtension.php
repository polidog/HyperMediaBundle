<?php

namespace Polidog\HypermediaBundle\DependencyInjection;


use Polidog\SimpleApiBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class HypermediaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container) :void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('hypermedia.hal_content_type', $config['hal_content_type']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
