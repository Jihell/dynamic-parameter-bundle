<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class JihelPluginDynamicParameterExtension
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class JihelPluginDynamicParameterExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->registerNamespaces($container);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('cache.yml');
        $loader->load('listener.yml');
        $loader->load('loader.yml');
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerNamespaces(ContainerBuilder $container)
    {
        if (!$container->hasParameter('jihel.plugin.dynamic_parameter.allowed_namespaces')) {
            $container->setParameter('jihel.plugin.dynamic_parameter.allowed_namespaces', null);
        }
        if (!$container->hasParameter('jihel.plugin.dynamic_parameter.denied_namespaces')) {
            $container->setParameter('jihel.plugin.dynamic_parameter.denied_namespaces', null);
        }
        if (!$container->hasParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache')) {
            $container->setParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache', true);
        }
        if (!$container->hasParameter('jihel.plugin.dynamic_parameter.table_prefix')) {
            $container->setParameter('jihel.plugin.dynamic_parameter.table_prefix', 'jihel_');
        }
    }
}
