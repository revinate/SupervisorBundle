<?php

namespace YZ\SupervisorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class YZSupervisorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $supervisorServers = null;
        if (isset($config['default_environment']) && isset($config['servers'][$config['default_environment']])) {
            $supervisorServers = $config['servers'][$config['default_environment']];
        }
        $container->setParameter('supervisor.servers', $supervisorServers);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
