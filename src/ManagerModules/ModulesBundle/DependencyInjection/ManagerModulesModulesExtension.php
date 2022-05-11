<?php


namespace App\ManagerModules\ModulesBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class ManagerModulesModulesExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );


        $loader->load('services.yaml');


    }

    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $container->setParameter('modules', array());
        $module = [];
        foreach ($bundles as $bandleName => $src) {
            $modules = $container->getParameter('modules');
            $module['bandleName'] = $bandleName;
            $module['src'] = $src;
            $modules[] = $module;
            $container->setParameter('modules', $modules);
        }
        
    }

}