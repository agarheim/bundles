<?php


namespace App\Payments\LiqPayRetailCrmBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
//use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


class PaymentsLiqPayRetailCrmExtension extends Extension //implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
   //     $configuration = new Configuration(true);
//
     //   $config = $this->processConfiguration($configuration, $configs);
    //    $config['parameters']['modules'];//['modules_settings'];
        // ... you'll load the files here later

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');


//        $loader->load('parameters.yaml');
//        $this->addAnnotatedClassesToCompile([
//            // you can define the fully qualified class names...
//            'App\\Controller\\DefaultController',
//            // ... but glob patterns are also supported:
//            '**Bundle\\Controller\\',
//
//            // ...
//        ]);

    }

    public function prepend(ContainerBuilder $container)
    {

        // ...
    }

}