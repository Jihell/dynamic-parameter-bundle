<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Cache;

use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Dumper\DynamicParameterDumper;
use Jihel\Plugin\DynamicParameterBundle\Model\DynamicParameterCacheInterface;

/**
 * Class ParameterCache
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterCache
{
    const templateLocation  = '/../Resources/views/Cache/DynamicParameterCache.php.twig';
    const templateClass     = 'Jihel\Cache\DynamicParameterBundle\%s\DynamicParameterCache';

    static protected $autoloadCalled = false;

    /**
     * @var string
     */
    protected $namespaceHash;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        $this
            ->loadNamespaceHash()
            ->registerAutoload()
        ;
    }

    /**
     * @return $this
     */
    protected function loadNamespaceHash()
    {
        $allowedNamespaces =
        $deniedNamespaces  = null;

        if (isset($_SERVER['SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__ALLOWED_NAMESPACES'])) {
            $allowedNamespaces = $_SERVER['SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__ALLOWED_NAMESPACES'];
        }

        if (isset($_SERVER['SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__DENIED_NAMESPACES'])) {
            $deniedNamespaces  = $_SERVER['SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__DENIED_NAMESPACES'];
        }

        $this->namespaceHash = '_'.md5($allowedNamespaces.'|'.$deniedNamespaces);
        return $this;
    }

    /**
     * @return $this
     *
     * Autoload class in cache
     */
    final private function registerAutoload()
    {
        if (false === static::$autoloadCalled) {
            spl_autoload_register(function($class) {
                if (false === strpos($class, 'Jihel')) {
                    return false;
                }
                $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
                $file  = $this->cacheDir.DIRECTORY_SEPARATOR.$class.'.php';
                if (file_exists($file)) {
                    include $file;
                    return true;
                }
                return false;
            });

            static::$autoloadCalled = true;
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return sprintf(static::templateClass, $this->namespaceHash);
    }

    /**
     * @return string
     */
    protected function getClassCacheDir()
    {
        $part = explode('\\', $this->getClassName());
        array_pop($part);
        return $this->cacheDir.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $part);
    }

    /**
     * @return mixed
     */
    protected function getClassFileName()
    {
        $part = explode('\\', $this->getClassName());
        return $this->getClassCacheDir().DIRECTORY_SEPARATOR.array_pop($part).'.php';
    }

    /**
     * @return bool
     */
    public function isCached()
    {
        return class_exists($this->getClassName());
    }

    /**
     * Load dynamic parameters from cache
     *
     * @return bool
     */
    public function loadFromCache()
    {
        $class = $this->getClassName();
        if (class_exists($class)) {
            /** @var DynamicParameterCacheInterface $cache */
            $cache = new $class();
            return $cache->getParameters();
        }
        return array();
    }

    /**
     * @param array $parameters
     * @return int
     * @throws Exception\UnwritableCacheException
     */
    public function createCache(array $parameters)
    {
        $dumper = new DynamicParameterDumper();
        $content = $dumper->dump(array('parameters' => $parameters, 'namespaceHash' => $this->namespaceHash));

        if (!is_dir($this->getClassCacheDir())) {
            mkdir($this->getClassCacheDir(), 0777, true);
        }

        // Atomic cache creation
        try {
            if (false === ($out = file_put_contents($this->getClassFileName().'.tmp', $content))) {
                throw new Exception\UnwritableCacheException(sprintf('Can\'t create cache file %s', $this->getClassFileName().'.tmp'));
            }
            if (false === rename($this->getClassFileName().'.tmp', $this->getClassFileName())) {
                throw new Exception\UnwritableCacheException(sprintf('Can\'t rename cache file %s', $this->getClassFileName().'.tmp'));
            }
        } catch (Exception\UnwritableCacheException $e) {
            $this->invalidateCache();
            throw $e;
        }

        return $out;
    }

    /**
     * @return bool
     */
    public function invalidateCache()
    {
        $file = $this->getClassFileName();
        if (file_exists($file)) {
            return unlink($this->getClassFileName());
        }
        if (file_exists($file.'.tmp')) {
            return unlink($this->getClassFileName());
        }
        return true;
    }
}
