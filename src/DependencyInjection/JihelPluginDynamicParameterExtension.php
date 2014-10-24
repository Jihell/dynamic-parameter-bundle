<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection;

use Jihel\Plugin\DynamicParameterBundle\Manager\CacheManager;
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
        $loader->load('listener.yml');
        $loader->load('manager.yml');
        $this->registerParameters($container);
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
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function registerParameters(ContainerBuilder $container)
    {
        /** @var CacheManager $cacheManager */
        $cacheManager = $container->get('jihel.plugin.dynamic_parameter.manager.cache');
        $cacheManager->loadFromCache();
    }
}
