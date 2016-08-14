<?php

namespace Polonairs\SmsiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class SmsiExtension extends Extension
{
    public function getAlias()
    {
        return "smsi";
    }
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter("polonairs.smsi.login", $config["login"]);
        $container->setParameter("polonairs.smsi.password", $config["password"]);
        $container->setParameter("polonairs.smsi.receiver", $config["receiver"]);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('smsi.xml');
    }
}
