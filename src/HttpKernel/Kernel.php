<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\HttpKernel;

use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Dumper\CustomContainerDumper;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bridge\ProxyManager\LazyProxy\PhpDumper\ProxyDumper;

/**
 * Class Kernel
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
abstract class Kernel extends BaseKernel
{
    const customContainerOverloadClassPrefix = 'Jihel';

    /**
     * Initializes the service container.
     *
     * The cached version of the service container is used when fresh, otherwise the
     * container is built.
     */
    protected function initializeContainer()
    {
        $customClass    = $this->getCustomContainerClass();
        $baseClass      = $this->getContainerClass();
        $customCache    = new ConfigCache($this->getCacheDir().'/'.$customClass.'.php', $this->debug);
        $baseCache      = new ConfigCache($this->getCacheDir().'/'.$baseClass.'.php', $this->debug);
        $fresh = true;
        if (!$baseCache->isFresh() || !$customCache->isFresh()) {
            $container = $this->buildContainer();
            if (!$container->hasParameter('jihel.plugin.dynamic_parameter.table_prefix')) {
                $container->setParameter('jihel.plugin.dynamic_parameter.table_prefix', 'jihel_');
            }
            $container->compile();
            $this->dumpContainer($baseCache, $container, $baseClass, $this->getContainerBaseClass());
            $this->dumpCustomContainer($customCache, $container, $baseClass);

            $fresh = false;
        }

        require_once $baseCache;
        require_once $customCache;

        $this->container = new $customClass();
        $this->container->set('kernel', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
    }

    /**
     * @return string
     */
    protected function getCustomContainerClass()
    {
        return static::customContainerOverloadClassPrefix.$this->getContainerClass();
    }

    /**
     * Dumps the service container to PHP code in the cache.
     *
     * @param ConfigCache      $cache     The config cache
     * @param ContainerBuilder $container The service container
     * @param string           $class     The name of the class to generate
     * @param string           $baseClass The name of the container's base class
     */
    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
    {
        // cache the container
        $dumper = new PhpDumper($container);

        if (class_exists('ProxyManager\Configuration')) {
            $dumper->setProxyDumper(new ProxyDumper());
        }

        $content = $dumper->dump(array('class' => $class, 'base_class' => $baseClass));
        if (!$this->debug) {
            $content = static::stripComments($content);
        }

        $cache->write($content, $container->getResources());
    }

    /**
     * Dumps the service container to PHP code in the cache.
     *
     * @param ConfigCache      $cache     The config cache
     * @param ContainerBuilder $container The service container
     * @param string           $baseClass The name of the parent class
     */
    protected function dumpCustomContainer(ConfigCache $cache, ContainerBuilder $container, $baseClass)
    {
        $dumper  = new CustomContainerDumper();
        $content = $dumper->dump(array('base_class' => $baseClass, 'prefix' => static::customContainerOverloadClassPrefix));
        if (!$this->debug) {
            $content = static::stripComments($content);
        }

        $cache->write($content, $container->getResources());
    }
}
