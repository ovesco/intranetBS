<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('intranet_parameters.yml');
        $loader->load('roles_parameters.yml');
        $loader->load('voters.yml');
        $loader->load('repositories.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        /*
         * On charge tout les config définie dans config.yml->app comme parametre.
         *
         * Exemple:
         *
         * #config.yml
         * app:
         *      bidon: coucou
         *
         * //in controllers
         * $container->getParameter('app.bidon');
         *
         */
        foreach($config as $key=>$param)
        {
            $container->setParameter( 'app.'.$key, $param);
        }




    }
}
