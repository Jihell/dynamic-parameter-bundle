<?php
/**
 * @package dynamic-parameter-bundle
 */
namespace Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

use Jihel\Plugin\DynamicParameterBundle\DependencyInjection\Loader\ParameterLoader;
use Jihel\Plugin\DynamicParameterBundle\Listener\DoctrineListener;
use Jihel\Plugin\DynamicParameterBundle\Manager\CacheManager;

/**
 * Class ParameterLoaderFactory
 *
 * @author Joseph LEMOINE <lemoine.joseph@gmail.com>
 * @link http://www.joseph-lemoine.fr
 */
class ParameterLoaderFactory
{
    /**
     * Constructor
     *
     * @param array $parameters = array()
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * @return ParameterLoader
     */
    public function get()
    {
        return new ParameterLoader(
            $this->getEntityManager(),
            $this->getCacheManager(),
            $this->getParameter('jihel.plugin.dynamic_parameter.allowed_namespaces'),
            $this->getParameter('jihel.plugin.dynamic_parameter.denied_namespaces'),
            $this->getParameter('jihel.plugin.dynamic_parameter.dynamic_parameter_cache'),
            $this->getParameter('kernel.environment')
        );
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getParameter($name)
    {
        if (!isset($this->parameters[$name])) {
            return;
        }
        return $this->parameters[$name];
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getEntityManager()
    {
        $params = array(
            'driver'    => $this->getParameter('database_driver'),
            'port'      => $this->getParameter('database_port'),
            'host'      => $this->getParameter('database_host'),
            'dbname'    => $this->getParameter('database_name'),
            'user'      => $this->getParameter('database_user'),
            'password'  => $this->getParameter('database_password'),
        );

        $entityPath = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Entity');
        $entityManager = EntityManager::create($params, Setup::createAnnotationMetadataConfiguration(
            array($entityPath), true, null, null, false
        ));

        // Register table prefix name
        $doctrineListener = new DoctrineListener($this->getParameter('jihel.plugin.dynamic_parameter.table_prefix'));
        $doctrineListener->registerEventManager($entityManager->getEventManager());

        return $entityManager;
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager()
    {
        return new CacheManager($this->getParameter('kernel.cache_dir'));
    }
}
